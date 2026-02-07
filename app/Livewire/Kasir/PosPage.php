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
                        $pq->with(['units'])->withSum('batches as total_stock', 'qty_sisa_base');
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

    // --- Search Logic ---
    public function updatedSearch()
    {
        if (strlen($this->search) < 2) {
            $this->searchResults = [];
            return;
        }

        $this->searchResults = \App\Models\Product::where('is_active', true)
            ->where(function($query) {
                $query->where('nama', 'like', '%' . $this->search . '%')
                      ->orWhere('kode_produk', 'like', '%' . $this->search . '%');
            })
            ->withSum('batches as estimated_stock', 'qty_sisa_base')
            ->take(8)
            ->get();

        // Auto-add if scanner sent Enter prematurely
        if ($this->pendingEnter && count($this->searchResults) === 1) {
            $this->addToCart($this->searchResults[0]->id);
            $this->pendingEnter = false;
        }
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
            app(\App\Services\SaleService::class)->addToCart($this->currentSale, $productId);
            
            $this->search = '';
            $this->searchResults = [];
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
            $available = $item->product->total_stock ?? 0;
            if ($item->qty_base > $available) {
                $this->dispatch('notify', message: "Stok {$item->product->nama} tidak mencukupi untuk pembayaran.", type: 'error');
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
        $this->successCashReceived = (int) $this->cashReceived;
        $this->successCashChange = (int) $this->cashChange;
        $this->successDate = now()->format('d-m-Y H:i');

        // Snapshot items for printing
        $this->successItems = $this->currentSale->items->map(function ($item) {
            return [
                'nama' => $item->product->nama,
                'qty' => $item->qty_base / $item->unit_multiplier,
                'unit' => $item->unit_label,
                'harga' => $item->harga_jual_per_unit * $item->unit_multiplier,
                'subtotal' => $item->subtotal
            ];
        })->toArray();
        
        $this->currentSale = null;
        $this->partialDebtAmount = 0;
        $this->cashReceived = 0;
        $this->cashChange = 0;
        $this->showCheckoutModal = false;
        $this->showSuccessModal = true;
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
