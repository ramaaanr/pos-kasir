<?php

namespace App\Livewire\Kasir;

use Livewire\Component;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Customer;
use App\Models\SalePayment;
use App\Models\ProductBatch;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

#[Layout('components.layouts.kasir', ['title' => 'Transaksi POS'])]
class PosPage extends Component
{
    // Transaction State
    public ?Sale $currentSale = null;
    public $search = '';
    public $searchResults = [];
    public $activeUnitId = null;       // currently selected unit ID for add-to-cart (null = base)
    public $activeProductUnits = [];   // units of the first search result

    // Modals State
    public $showCheckoutModal = false;
    public $showCancelModal = false;
    public $showNavGuardModal = false;
    public $showSuccessModal = false;
    public $showErrorModal = false;
    public $errorMessage = '';
    
    public $checkoutStep = 1; // 1: Review, 2: Payment, 3: Customer
    public $paymentMethod = 'cash'; // 'cash' or 'debt'
    
    // Customer & Debt Form
    public $customerName = '';
    public $customerPhone = '';
    public $customerAddress = '';
    public $jaminan = '';
    public $partialDebtAmount = 0;

    // Cash Calculation (Checkout Step 2)
    public $cashReceived = 0;
    public $cashChange = 0;

    // Barcode Scanner Helper
    public $pendingEnter = false;

    public function updatedCashReceived()
    {
        $total = $this->currentSale->total ?? 0;
        $this->cashChange = max(0, (int) $this->cashReceived - $total);
    }

    public function selectFirstResult()
    {
        if (count($this->searchResults) > 0) {
            $this->addToCart($this->searchResults[0]->id);
            $this->pendingEnter = false;
        } else {
            // If no results yet, mark as pending to auto-add as soon as it arrives
            $this->pendingEnter = true;
        }
    }

    public function mount()
    {
        // Try to resume existing draft first (Persistence)
        $this->loadCurrentSale();
    }

    private function loadCurrentSale()
    {
        // Check for active draft for this user
        $sale = Sale::where('user_id', auth()->id())
            ->where('status', 'draft')
            ->first();

        if ($sale) {
            $this->currentSale = Sale::where('id', $sale->id)
                ->with(['items' => function($q) {
                    $q->with(['product' => function($pq) {
                    $pq->with(['units'])
                        ->withSum('batches as total_stock', 'qty_sisa_base')
                        ->withSum(['batches as bonus_stock' => function ($bq) {
                            $bq->where('is_bonus', true);
                        }], 'qty_sisa_base');
                    }]);
                }])
                ->first();
        } else {
            $this->currentSale = null;
        }
    }

    public function hydrate()
    {
        // Ensure total_stock SUM attribute is re-loaded on every request
        // Standard hydration loses attributes added via withSum()
        $this->loadCurrentSale();
    }

    // --- Computed Properties for UI Summary ---
    public function getSubtotalProperty()
    {
        if (!$this->currentSale || !$this->currentSale->items) return 0;
        return $this->currentSale->items->where('is_bonus_item', false)->sum('subtotal');
    }

    public function getBonusDiscountProperty()
    {
        if (!$this->currentSale || !$this->currentSale->items) return 0;
        $bonusItems = $this->currentSale->items->where('is_bonus_item', true);

        $discount = 0;
        foreach ($bonusItems as $item) {
            $discount += (int)($item->qty_base * $item->product->harga_jual_default);
        }
        return $discount;
    }

    public function getBonusCountProperty()
    {
        if (!$this->currentSale || !$this->currentSale->items) return 0;
        return (int)$this->currentSale->items->where('is_bonus_item', true)->sum('qty_base');
    }

    public function getAvailableBonusStock($productId)
    {
        if (!$this->currentSale) return 0;
        return app(\App\Services\SaleService::class)->getAvailableBonusStock($productId, $this->currentSale->id);
    }

    // --- Search Logic ---
    public function updatedSearch()
    {
        if (strlen($this->search) < 2) {
            $this->searchResults = [];
            $this->activeUnitId = null;
            $this->activeProductUnits = [];
            return;
        }

        $this->searchResults = \App\Models\Product::where('is_active', true)
            ->where(function($query) {
                $query->where('nama', 'like', '%' . $this->search . '%')
                      ->orWhere('kode_produk', 'like', '%' . $this->search . '%');
            })
            ->withSum('batches as estimated_stock', 'qty_sisa_base')
            ->with('units')
            ->take(8)
            ->get();

        // Populate unit pills from first result
        if ($this->searchResults->count() > 0) {
            $first = $this->searchResults->first();
            // Build list: base unit first, then extra units
            $units = [['id' => null, 'label' => $first->base_unit, 'multiplier' => 1]];
            foreach ($first->units as $u) {
                $units[] = ['id' => $u->id, 'label' => $u->label, 'multiplier' => (int)$u->multiplier];
            }
            $this->activeProductUnits = $units;

            // Keep existing selection if it's valid for this product
            $validUnitIds = collect($units)->pluck('id')->toArray();
            if (!in_array($this->activeUnitId, $validUnitIds)) {
                $this->activeUnitId = null; // Default to base unit
            }
        } else {
            $this->activeProductUnits = [];
            $this->activeUnitId = null;
        }

        // Auto-add if scanner sent Enter prematurely
        if ($this->pendingEnter && count($this->searchResults) === 1) {
            $this->addToCart($this->searchResults[0]->id);
            $this->pendingEnter = false;
        }
    }

    public function setActiveUnit(?int $unitId)
    {
        $this->activeUnitId = $unitId;
    }

    // --- Cart Management ---
    public function startNewTransaction()
    {
        $this->currentSale = Sale::create([
            'invoice_number' => 'INV-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4)),
            'user_id' => auth()->id(),
            'status' => 'draft',
            'total' => 0,
            'total_paid' => 0,
            'payment_method' => 'cash',
        ]);
        
        $this->loadCurrentSale();
        $this->dispatch('notify', message: 'Transaksi baru dimulai.', type: 'success');
    }

    public function addToCart($productId)
    {
        if (!$this->currentSale) return;

        try {
            app(\App\Services\SaleService::class)->addToCart(
                $this->currentSale,
                $productId,
                1,
                $this->activeUnitId  // pass the active unit ID
            );
            
            $this->search = '';
            $this->searchResults = [];
            $this->activeProductUnits = [];
            $this->activeUnitId = null;
            $this->pendingEnter = false;
            $this->loadCurrentSale();
        } catch (\Exception $e) {
            $this->dispatch('notify', message: $e->getMessage(), type: 'error');
        }
    }

    public function adjustQty($itemId, $delta)
    {
        $item = SaleItem::find($itemId);
        if (!$item) return;

        try {
            // BUG 2: Ensure visual delta is integer
            if (floor($delta) != $delta) {
                throw new \Exception("Kuantitas harus berupa angka bulat.");
            }

            $baseDelta = (int)($delta * $item->unit_multiplier);
            app(\App\Services\SaleService::class)->updateItemQty($item, $item->qty_base + $baseDelta);
            $this->loadCurrentSale();
        } catch (\Exception $e) {
            $this->dispatch('notify', message: $e->getMessage(), type: 'error');
        }
    }

    public function updateQty($itemId, $visualQty)
    {
        $item = SaleItem::find($itemId);
        if (!$item) return;

        try {
            // BUG 2: Strict Integer Validation
            if (!is_numeric($visualQty) || floor($visualQty) != $visualQty) {
                throw new \Exception("Kuantitas harus berupa angka bulat.");
            }

            $baseQty = (int)((float)$visualQty * $item->unit_multiplier);
            app(\App\Services\SaleService::class)->updateItemQty($item, $baseQty);
            $this->loadCurrentSale();
        } catch (\Exception $e) {
            $this->dispatch('notify', message: $e->getMessage(), type: 'error');
        }
    }

    public function changeUnit($itemId, $unitId)
    {
        $item = SaleItem::find($itemId);
        if (!$item) return;

        try {
            $unit = $unitId === 'base' ? null : \App\Models\ProductUnit::find($unitId);
            app(\App\Services\SaleService::class)->updateItemUnit($item, $unit);
            $this->loadCurrentSale();
        } catch (\Exception $e) {
            $this->dispatch('notify', message: $e->getMessage(), type: 'error');
        }
    }

    public function removeItem($itemId)
    {
        $item = SaleItem::find($itemId);
        if($item) {
            $item->delete();
            $this->currentSale->update(['total' => $this->currentSale->items()->sum('subtotal')]);
        }
        $this->loadCurrentSale();
    }

    public function toggleBonus($itemId)
    {
        $item = SaleItem::find($itemId);
        if (!$item) return;

        try {
            app(\App\Services\SaleService::class)->toggleBonusItem($item);
            $this->loadCurrentSale();
        } catch (\Exception $e) {
            $this->dispatch('notify', message: $e->getMessage(), type: 'error');
        }
    }

    public function cancelTransaction()
    {
        if (!$this->currentSale) return;

        try {
            app(\App\Services\SaleService::class)->cancelSale($this->currentSale);
            $this->currentSale = null;
            $this->showCancelModal = false;
            $this->showNavGuardModal = false;
            $this->dispatch('notify', message: 'Transaksi dibatalkan.', type: 'info');
        } catch (\Exception $e) {
            $this->dispatch('notify', message: $e->getMessage(), type: 'error');
        }
    }

    // --- Checkout Flow ---
    public function openCheckout()
    {
        if (!$this->currentSale || $this->currentSale->items->count() === 0) return;
        
        // Re-validate stock before opening modal
        $this->loadCurrentSale();
        foreach($this->currentSale->items as $item) {
            if ($item->qty_base > ($item->product->total_stock ?? 0)) {
                $this->dispatch('notify', message: "Gagal: Stok '{$item->product->nama}' tidak mencukupi.", type: 'error');
                return;
            }
        }

        $this->showCheckoutModal = true;
        $this->checkoutStep = 1;
    }

    public function nextStep()
    {
        if ($this->checkoutStep === 1) {
            $this->checkoutStep = 2;
        } elseif ($this->checkoutStep === 2) {
            if ($this->paymentMethod === 'cash') {
                // Validate cash received
                if ($this->cashReceived < $this->currentSale->total) {
                    $this->dispatch('notify', message: 'Uang yang diterima kurang!', type: 'error');
                    return;
                }
                $this->finalizeTransaction();
            } else {
                $this->checkoutStep = 3;
            }
        }
    }

    public function prevStep()
    {
        $this->checkoutStep--;
    }

    public function handleBack()
    {
        if ($this->currentSale && $this->currentSale->items->count() > 0) {
            $this->showNavGuardModal = true;
        } else {
            $this->redirect(route('kasir.dashboard'), navigate: true);
        }
    }

    public function cancelTransactionFromGuard()
    {
        $this->cancelTransaction();
        $this->redirect(route('kasir.dashboard'), navigate: true);
    }

    public function finalizeTransaction()
    {
        $this->loadCurrentSale();
        
        // UI-Level Guard for Stock
        foreach($this->currentSale->items as $item) {
            $avail = $item->is_bonus_item
                ? $this->getAvailableBonusStock($item->product_id)
                : ($item->product->total_stock ?? 0);

            if ($item->qty_base > $avail) {
                $type = $item->is_bonus_item ? 'Stok bonus' : 'Stok';
                $this->dispatch('notify', message: "{$type} {$item->product->nama} tidak mencukupi ({$avail} tersedia).", type: 'error');
                return;
            }
        }

        try {
            app(\App\Services\SaleService::class)->finalizeSale($this->currentSale, [
                'method' => $this->paymentMethod,
                'customer_name' => $this->customerName,
                'customer_phone' => $this->customerPhone,
                'customer_address' => $this->customerAddress,
                'jaminan' => $this->jaminan,
                'partial_amount' => $this->paymentMethod === 'debt' ? (int) $this->partialDebtAmount : 0,
                'cash_received' => $this->paymentMethod === 'cash' ? (int) $this->cashReceived : 0,
                'cash_change' => $this->paymentMethod === 'cash' ? (int) $this->cashChange : 0,
            ]);

            $this->finalizeSuccessState();
            
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
            $this->showCheckoutModal = false;
            $this->showErrorModal = true;
        }
    }

    // Snapshot state for success modal (Bug 3)
    public $successInvoice = '';
    public $successTotal = 0;
    public $successMethod = '';
    public $successCustomer = '';
    public $successPartialAmount = 0;
    public $successCashReceived = 0;
    public $successCashChange = 0;
    public $successItems = [];
    public $successDate = '';

    private function finalizeSuccessState()
    {
        $this->successInvoice = $this->currentSale->invoice_number;
        $this->successTotal = $this->currentSale->total;
        $this->successMethod = $this->paymentMethod;
        $this->successCustomer = $this->customerName;
        $this->successPartialAmount = (int) $this->partialDebtAmount;
        $this->successCashReceived = (int) $this->cashReceived;
        $this->successCashChange = (int) $this->cashChange;
        $this->successDate = now()->format('d-m-Y H:i');

        // Snapshot items for printing (backup for view)
        $this->successItems = $this->currentSale->items->map(function ($item) {
            return [
                'nama' => $item->product->nama,
                'qty' => $item->qty_base / $item->unit_multiplier,
                'unit' => $item->unit_label,
                'harga' => $item->harga_jual_per_unit,
                'subtotal' => $item->subtotal,
                'is_bonus_item' => $item->is_bonus_item,
                'regular_price' => $item->product->harga_jual_default * $item->unit_multiplier,
            ];
        })->toArray();
        
        // Auto Print ESC/POS (DISABLED REQUESTED BY USER)
        /*
        try {
            app(\App\Services\PrinterService::class)->printInvoice($this->currentSale);
            $this->dispatch('notify', message: 'Struk berhasil dicetak.', type: 'success');
        } catch (\Exception $e) {
            $printerUrl = config('app.printer_url');
            $this->dispatch('notify', message: "Gagal mencetak struk ke '$printerUrl': " . $e->getMessage(), type: 'warning');
        }
        */

        $this->currentSale = null;
        $this->partialDebtAmount = 0;
        $this->cashReceived = 0;
        $this->cashChange = 0;
        $this->showCheckoutModal = false;
        $this->showSuccessModal = true;
    }

    public function reprintLastInvoice()
    {
        try {
            $lastSale = Sale::where('invoice_number', $this->successInvoice)->first();
            if ($lastSale) {
                app(\App\Services\PrinterService::class)->printInvoice($lastSale);
                $this->dispatch('notify', message: 'Struk dicetak ulang.', type: 'success');
            }
        } catch (\Exception $e) {
            $printerUrl = config('app.printer_url');
            $this->dispatch('notify', message: "Gagal mencetak ulang ke '$printerUrl': " . $e->getMessage(), type: 'error');
        }
    }

    public function resetPos()
    {
        $this->currentSale = null;
        $this->showSuccessModal = false;
        $this->redirect(route('kasir.pos'), navigate: true);
    }

    public function render()
    {
        return view('livewire.kasir.pos-page');
    }
}
