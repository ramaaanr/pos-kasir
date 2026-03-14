<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\ProductBatch;
use App\Models\ProductBatchLog;
use App\Models\Customer;
use App\Models\Debt;
use App\Models\DebtPayment;
use App\Models\SalePayment;
use Illuminate\Support\Facades\DB;
use Exception;

class SaleService
{
    /**
     * Add a product to a draft sale.
     */
    public function addToCart(Sale $sale, int $productId, int $qty = 1)
    {
        if ($sale->status !== 'draft') {
            throw new Exception("Cannot modify a non-draft sale.");
        }

        $product = Product::findOrFail($productId);
        
        $item = SaleItem::where('sale_id', $sale->id)
            ->where('product_id', $productId)
            ->where('is_bonus_item', false) // Always add to regular item row
            ->whereNull('product_batch_id') // Correct: Draft items have null batch
            ->first();

        if ($item) {
            // Increment by $qty visual unit (based on current multiplier)
            $increment = (int) ($qty * $item->unit_multiplier);
            $item->increment('qty_base', $increment);
            $item->update(['subtotal' => $item->qty_base * $item->harga_jual_per_unit]);
        } else {
            SaleItem::create([
                'sale_id' => $sale->id,
                'product_id' => $product->id,
                'qty_base' => $qty,
                'is_bonus_item' => false,
                'unit_label' => $product->base_unit,
                'unit_multiplier' => 1,
                'harga_jual_per_unit' => $product->harga_jual_default,
                'subtotal' => $product->harga_jual_default * $qty,
            ]);
        }

        $this->updateSaleTotal($sale);
    }

    /**
     * Update qty of a sale item (base qty).
     */
    public function updateItemQty(SaleItem $item, int $qty)
    {
        if ($item->sale->status !== 'draft') {
            throw new Exception("Cannot modify items of a finalized sale.");
        }

        if ($qty < 1) {
            $item->delete();
        } else {
            // BONUS CHECK (Validation moved to finalization to allow Warning UI)
            if ($item->is_bonus_item) {
                // Allow in draft, checked again in finalizeSale
            }

            $item->update([
                'qty_base' => $qty,
                'subtotal' => $qty * $item->harga_jual_per_unit
            ]);
        }

        $this->updateSaleTotal($item->sale);
    }

    /**
     * Toggle bonus status of a sale item.
     */
    public function toggleBonusItem(SaleItem $item)
    {
        if ($item->sale->status !== 'draft') {
            throw new Exception("Cannot modify items of a finalized sale.");
        }

        $sale = $item->sale;
        $productId = $item->product_id;
        $targetIsBonus = !$item->is_bonus_item;

        // Find the "opposite" item (if current is regular, find bonus; if current is bonus, find regular)
        $oppositeItem = SaleItem::where('sale_id', $sale->id)
            ->where('product_id', $productId)
            ->where('is_bonus_item', $targetIsBonus)
            ->whereNull('product_batch_id')
            ->first();

        // SPLIT/MERGE LOGIC: Bonus availability checked in finalization to allow Warning UI
        if ($targetIsBonus) {
            // Allow in draft, checked again in finalizeSale
        }

        // SPLIT LOGIC: If qty > 1, move only 1 unit to the opposite category
        if ($item->qty_base > 1) {
            $item->decrement('qty_base', 1);
            $item->update(['subtotal' => $item->qty_base * $item->harga_jual_per_unit]);

            if ($oppositeItem) {
                $oppositeItem->increment('qty_base', 1);
                $oppositeItem->update(['subtotal' => $oppositeItem->qty_base * $oppositeItem->harga_jual_per_unit]);
            } else {
                $newPrice = $targetIsBonus ? 0 : $item->product->harga_jual_default;
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $productId,
                    'qty_base' => 1,
                    'is_bonus_item' => $targetIsBonus,
                    'unit_label' => $item->unit_label,
                    'unit_multiplier' => $item->unit_multiplier,
                    'harga_jual_per_unit' => $newPrice,
                    'subtotal' => $newPrice,
                ]);
            }
        } else {
            // MERGE/TOGGLE LOGIC: If qty == 1, merge with existing opposite or just toggle
            if ($oppositeItem) {
                $oppositeItem->increment('qty_base', 1);
                $oppositeItem->update(['subtotal' => $oppositeItem->qty_base * $oppositeItem->harga_jual_per_unit]);
                $item->delete();
            } else {
                $isBonus = !$item->is_bonus_item;
                $newPrice = 0;
                if (!$isBonus) {
                    // Back to regular, find unit price if any
                    $unit = ProductUnit::where('product_id', $productId)
                        ->where('label', $item->unit_label)
                        ->first();
                    $newPrice = $unit->harga_jual ?? ($item->product->harga_jual_default * $item->unit_multiplier);
                }

                $item->update([
                    'is_bonus_item' => $isBonus,
                    'harga_jual_per_unit' => $newPrice,
                    'subtotal' => (int)($item->qty_base * $newPrice)
                ]);
            }
        }

        $this->updateSaleTotal($sale);
    }

    /**
     * Get available bonus stock for a product, accounting for items already in a draft sale.
     */
    public function getAvailableBonusStock(int $productId, int $saleId): int
    {
        $totalBonusStock = ProductBatch::where('product_id', $productId)
            ->where('is_bonus', true)
            ->sum('qty_sisa_base');

        // Subtract bonus items from ALL other draft sales, excluding the current one.
        $reservedBonus = SaleItem::where('product_id', $productId)
            ->where('is_bonus_item', true)
            ->where('sale_id', '!=', $saleId) // Exclude items from the current sale
            ->whereHas('sale', function ($q) {
                $q->where('status', 'draft');
            })
            ->sum('qty_base');

        return (int)max(0, $totalBonusStock - $reservedBonus);
    }

    /**
     * Update unit of a sale item.
     */
    public function updateItemUnit(SaleItem $item, ?ProductUnit $unit = null)
    {
        if ($item->sale->status !== 'draft') {
            throw new Exception("Cannot modify items of a finalized sale.");
        }

        if ($unit) {
            $label = $unit->label;
            $multiplier = $unit->multiplier;
            $harga_jual = $unit->harga_jual ?? ($item->product->harga_jual_default * $multiplier);
        } else {
            // Revert to base unit
            $label = $item->product->base_unit;
            $multiplier = 1;
            $harga_jual = $item->product->harga_jual_default;
        }

        $newQtyBase = (int)($multiplier); // Reset to 1 unit of the new selection

        // Validation: Ensure total available stock is enough for the new unit's base quantity
        $totalAvailable = ProductBatch::where('product_id', $item->product_id)
            ->sum('qty_sisa_base');
        
        if ($totalAvailable < $newQtyBase) {
            throw new Exception("Stok tidak mencukupi untuk unit '{$label}'. (Tersedia: {$totalAvailable} {$item->product->base_unit})");
        }

        $item->update([
            'unit_label' => $label,
            'unit_multiplier' => $multiplier,
            'qty_base' => $newQtyBase,
            'harga_jual_per_unit' => $harga_jual,
            'subtotal' => $harga_jual // Since qty is reset to 1 (of this unit)
        ]);

        $this->updateSaleTotal($item->sale);
    }

    /**
     * Finalize the sale using FIFO logic and separate payment processing.
     */
    public function finalizeSale(Sale $sale, array $paymentData)
    {
        return DB::transaction(function () use ($sale, $paymentData) {
            $this->validateSale($sale, $paymentData);
            
            $this->processStockFIFO($sale);
            
            $this->finalizePayment($sale, $paymentData);

            return $sale;
        });
    }

    /**
     * Step 1: Validate sale status and basic stock availability.
     */
    protected function validateSale(Sale $sale, array $paymentData)
    {
        if ($sale->status !== 'draft') {
            throw new Exception("Sale is already finalized.");
        }

        $sale->load('items.product');

        if ($sale->items->count() === 0) {
            throw new Exception("Keranjang masih kosong.");
        }

        foreach ($sale->items as $item) {
            $totalAvailable = ProductBatch::where('product_id', $item->product_id)
                ->sum('qty_sisa_base');
            
            if ($totalAvailable < $item->qty_base) {
                throw new Exception("Stok {$item->product->nama} tidak mencukupi. (Tersedia: {$totalAvailable})");
            }
        }
    }

    /**
     * Step 2: Handle FIFO stock deduction and recording.
     */
    protected function processStockFIFO(Sale $sale)
    {
        foreach ($sale->items as $item) {
            $qtyNeeded = $item->qty_base;

            $batches = ProductBatch::where('product_id', $item->product_id)
                ->where('qty_sisa_base', '>', 0)
                ->orderByRaw('is_bonus DESC')
                ->orderBy('tanggal_masuk', 'asc')
                ->get();

            foreach ($batches as $batch) {
                if ($qtyNeeded <= 0) break;

                $deduct = min($batch->qty_sisa_base, $qtyNeeded);
                
                $qtyBefore = $batch->qty_sisa_base;
                $batch->decrement('qty_sisa_base', $deduct);
                $qtyAfter = $batch->qty_sisa_base;

                // Log Stock Change
                ProductBatchLog::create([
                    'product_batch_id' => $batch->id,
                    'user_id' => auth()->id(),
                    'action' => 'sale',
                    'qty_change' => -$deduct,
                    'qty_before' => $qtyBefore,
                    'qty_after' => $qtyAfter,
                    'description' => "Penjualan Invoice #{$sale->invoice_number}",
                ]);

                // Record internal mapping for FIFO/Audit
                \App\Models\SaleItemBatch::create([
                    'sale_item_id' => $item->id,
                    'product_batch_id' => $batch->id,
                    'qty_base' => $deduct,
                    'harga_beli_per_unit' => $batch->harga_beli_per_unit,
                ]);

                $qtyNeeded -= $deduct;
            }

            // Update last_sold_at for the product
            $item->product->update(['last_sold_at' => now()]);

            // Note: We NO LONGER delete the item or split it into multiple SaleItems.
            // One SaleItem = One Row on Invoice.
        }
    }

    /**
     * Step 3: Handle payment logic based on method.
     */
    protected function finalizePayment(Sale $sale, array $paymentData)
    {
        $method = $paymentData['method'] ?? 'cash';

        if ($method === 'cash') {
            $this->processCashPayment($sale, $paymentData);
        } elseif ($method === 'debt') {
            $this->processDebtPayment($sale, $paymentData);
        } else {
            throw new Exception("Metode pembayaran '{$method}' tidak didukung.");
        }
    }

    protected function processCashPayment(Sale $sale, array $paymentData = [])
    {
        $sale->update([
            'total_paid' => $sale->total,
            'payment_method' => 'cash',
            'status' => 'completed',
            'cash_received' => $paymentData['cash_received'] ?? $sale->total,
            'cash_change' => $paymentData['cash_change'] ?? 0,
        ]);

        SalePayment::create([
            'sale_id' => $sale->id,
            'user_id' => auth()->id(),
            'amount' => $sale->total,
            'paid_at' => now(),
        ]);
    }

    protected function processDebtPayment(Sale $sale, array $paymentData)
    {
        // 1. Identify/Create Customer
        if ($paymentData['customer_phone']) {
            $customer = Customer::firstOrCreate(
                ['no_hp' => $paymentData['customer_phone']],
                [
                    'nama' => $paymentData['customer_name'],
                    'alamat' => $paymentData['customer_address'] ?? null
                ]
            );
        } else {
            // Find by name if phone is missing, ensuring we don't accidentally match someone with a phone
            $customer = Customer::where('nama', $paymentData['customer_name'])
                ->where(function ($q) {
                    $q->whereNull('no_hp')->orWhere('no_hp', '');
                })
                ->first();

            if (!$customer) {
                $customer = Customer::create([
                    'nama' => $paymentData['customer_name'],
                    'no_hp' => null,
                    'alamat' => $paymentData['customer_address'] ?? null
                ]);
            }
        }

        $partialAmount = (int) ($paymentData['partial_amount'] ?? 0);
        $totalDebt = $sale->total - $partialAmount;

        // 2. Create Debt Record
        $debt = Debt::create([
            'sale_id' => $sale->id,
            'customer_id' => $customer->id,
            'jaminan' => $paymentData['jaminan'],
            'amount' => $sale->total,
            'status' => $partialAmount > 0 ? 'PARTIAL' : 'OPEN',
        ]);

        // 3. Create initial DebtPayment if partialAmount > 0
        if ($partialAmount > 0) {
            DebtPayment::create([
                'debt_id' => $debt->id,
                'customer_id' => $customer->id,
                'user_id' => auth()->id(),
                'amount' => $partialAmount,
                'payment_method' => 'cash',
                'paid_at' => now(),
                'note' => 'Pembayaran awal saat transaksi',
            ]);
        }

        // 4. Update Customer total_debt (only the remaining portion)
        $customer->increment('total_debt', $totalDebt);

        // 5. Update Sale status
        $sale->update([
            'customer_id' => $customer->id,
            'total_paid' => $partialAmount,
            'payment_method' => 'debt',
            'status' => 'completed'
        ]);

        // 6. Record SalePayment for the partial amount if exists
        if ($partialAmount > 0) {
            SalePayment::create([
                'sale_id' => $sale->id,
                'user_id' => auth()->id(),
                'amount' => $partialAmount,
                'paid_at' => now(),
            ]);
        }
    }

    /**
     * Cancel a sale.
     */
    public function cancelSale(Sale $sale)
    {
        if ($sale->status !== 'draft') {
            throw new Exception("Cannot cancel a non-draft sale.");
        }

        $sale->update(['status' => 'cancelled']);
    }

    /**
     * Recalculate and update sale total.
     */
    private function updateSaleTotal(Sale $sale)
    {
        $total = $sale->items()->sum('subtotal');
        $sale->update(['total' => $total]);
    }
}
