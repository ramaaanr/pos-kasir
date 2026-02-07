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
            $item->update([
                'qty_base' => $qty,
                'subtotal' => $qty * $item->harga_jual_per_unit
            ]);
        }

        $this->updateSaleTotal($item->sale);
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
        } else {
            // Revert to base unit
            $label = $item->product->base_unit;
            $multiplier = 1;
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
            'subtotal' => $newQtyBase * $item->harga_jual_per_unit
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
        $customer = Customer::firstOrCreate(
            ['no_hp' => $paymentData['customer_phone']],
            [
                'nama' => $paymentData['customer_name'],
                'alamat' => $paymentData['customer_address'] ?? null
            ]
        );

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
