<?php

namespace App\Livewire\StockAdjustment;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\StockAdjustment;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Services\StockAdjustmentService;

class StockAdjustmentList extends Component
{
    use WithPagination;

    public $showModal = false;
    public $step = 1;
    
    // Header Data
    public $reason = '';
    public $tanggal = '';
    
    // Product Search & Selection
    public $productSearch = '';
    public $selectedProductId = null;
    
    // Staging Area
    // Structure: [ productId => [ 'name' => '...', 'batches' => [ batchId => [ ...data ] ] ] ]
    public $stagedItems = [];

    // Summary
    public $totalAffectedBatches = 0;
    public $totalNetChange = 0;

    protected $rules = [
        'reason' => 'required|min:3',
        'tanggal' => 'required|date',
    ];

    public function mount()
    {
        $this->tanggal = date('Y-m-d');
    }

    public function openModal()
    {
        $this->reset(['reason', 'stagedItems', 'selectedProductId', 'productSearch', 'totalAffectedBatches', 'totalNetChange']);
        $this->tanggal = date('Y-m-d');
        $this->step = 1;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    // --- Wizard Logic ---
    // Dummy properties for backward compatibility with stale client state
    public $showDetailModal = false;
    public $selectedAdjustmentId = null;
    public $adjustmentDetail = null;

    public function openDetailModal($id) { /* Deprecated */ }
    public function closeDetailModal() { $this->showDetailModal = false; }

    public function nextStep()
    {
        $this->validate([
            'reason' => 'required|min:3',
            'tanggal' => 'required',
        ]);
        
        $this->step = 2;
    }

    public function getFilteredProductsProperty()
    {
        if (strlen($this->productSearch) < 2) {
            return [];
        }

        return Product::where('is_active', true)
            ->where(function($q) {
                $q->where('nama', 'like', '%' . $this->productSearch . '%')
                  ->orWhere('kode_produk', 'like', '%' . $this->productSearch . '%');
            })
            ->limit(5)
            ->get();
    }

    public function selectProduct($productId)
    {
        $product = Product::with(['batches' => function($q) {
            $q->where('qty_sisa_base', '>', 0)->orderBy('tanggal_masuk', 'asc');
        }])->find($productId);

        if (!$product) return;

        // Initialize staging for this product if not exists
        if (!isset($this->stagedItems[$productId])) {
            $batches = [];
            foreach ($product->batches as $batch) {
                $batches[$batch->id] = [
                    'id' => $batch->id,
                    'code' => $batch->batch_code,
                    'tanggal_masuk' => $batch->tanggal_masuk->format('d-m-Y'),
                    'qty_current' => $batch->qty_sisa_base,
                    'qty_new' => $batch->qty_sisa_base, // Default to current
                ];
            }

            $this->stagedItems[$productId] = [
                'name' => $product->nama,
                'batches' => $batches
            ];
        }

        $this->selectedProductId = $productId;
        $this->productSearch = ''; // Clear search
        $this->calculateSummary();
    }

    public function updateBatchQty($productId, $batchId, $newQty)
    {
        // Ensure newQty is integer and non-negative
        $newQty = max(0, (int)$newQty);
        
        if (isset($this->stagedItems[$productId]['batches'][$batchId])) {
            $this->stagedItems[$productId]['batches'][$batchId]['qty_new'] = $newQty;
            $this->calculateSummary();
        }
    }

    public function removeProduct($productId)
    {
        unset($this->stagedItems[$productId]);
        if ($this->selectedProductId == $productId) {
            $this->selectedProductId = null;
        }
        $this->calculateSummary();
    }

    public function calculateSummary()
    {
        $this->totalAffectedBatches = 0;
        $this->totalNetChange = 0;

        foreach ($this->stagedItems as $product) {
            foreach ($product['batches'] as $batch) {
                if ($batch['qty_new'] != $batch['qty_current']) {
                    $this->totalAffectedBatches++;
                    $this->totalNetChange += ($batch['qty_new'] - $batch['qty_current']);
                }
            }
        }
    }

    public function save()
    {
        $this->validate();
        
        // Ensure at least one change
        if ($this->totalAffectedBatches === 0) {
            $this->addError('stagedItems', 'Belum ada perubahan stok yang dilakukan.');
            return;
        }

        try {
            $service = app(StockAdjustmentService::class);
            
            $service->createAdjustment(
                ['reason' => $this->reason, 'tanggal' => $this->tanggal],
                $this->stagedItems
            );

            $this->showModal = false;
            $this->dispatch('toast', ['type' => 'success', 'message' => 'Stock adjustment berhasil disimpan']);
        } catch (\Exception $e) {
            $this->dispatch('toast', ['type' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function render()
    {
        $adjustments = StockAdjustment::with(['user', 'items.batch.product'])
            ->latest()
            ->paginate(10);

        return view('livewire.stock-adjustment.stock-adjustment-list', [
            'adjustments' => $adjustments
        ])->layout('components.layouts.admin', [
            'title' => 'Stock Adjustment',
            'subtitle' => 'Manajemen koreksi stok produk'
        ]);
    }
}
