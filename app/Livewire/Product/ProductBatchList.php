<?php

namespace App\Livewire\Product;

use App\Models\Product;
use App\Models\ProductBatch;
use Livewire\Component;
use Livewire\WithPagination;

class ProductBatchList extends Component
{
    use WithPagination;

    // Filters & Search
    public $search = '';
    public $product_filter = 'all';
    public $perPage = 10;

    // Modal State
    public $showModal = false;
    public $showDetailModal = false;
    public $showDeleteModal = false;
    public $selectedBatchId = null;
    public $isEdit = false;

    // Form Data (UI State)
    public $productSearch = '';
    public $selectedProduct = '';
    public $unit_id = '';
    public $batch_code = '';
    public $harga_beli = 0;
    public $margin = 0; // Now Nominal (Rp)
    public $harga_jual = 0;
    public $qty_masuk = 0;
    public $tanggal_masuk = '';
    
    // Master Product Reference (for comparison)
    public $master_harga_beli = 0;
    public $master_harga_jual = 0;
    public $showConfirmMasterUpdate = false;

    // Derived Data
    public $batchDetail = null; // Used for Detail Modal
    public $availableUnits = [];
    public $isCalculating = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'product_filter' => ['except' => 'all'],
    ];

    public function mount()
    {
        $this->tanggal_masuk = date('Y-m-d');
    }

    public function openModal(?int $id = null)
    {
        $this->resetValidation();
        $service = app(\App\Services\ProductBatchService::class);

        if ($id) {
            $this->isEdit = true;
            $this->selectedBatchId = $id;
            $batch = ProductBatch::with('product.units')->findOrFail($id);
            $this->selectedProduct = $batch->product_id;
            $this->productSearch = $batch->product->nama;
            
            // Master reference from product
            $this->master_harga_beli = $batch->product->harga_beli_default;
            $this->master_harga_jual = $batch->product->harga_jual_default;

            $this->batch_code = $batch->batch_code;
            $this->harga_beli = $batch->harga_beli_per_unit;
            $this->harga_jual = $batch->harga_jual_per_unit;
            $this->margin = $this->harga_jual - $this->harga_beli;
            
            $this->qty_masuk = $batch->qty_masuk_base;
            $this->tanggal_masuk = $batch->tanggal_masuk->format('Y-m-d');
            $this->availableUnits = $batch->product->units;
        } else {
            $this->isEdit = false;
            $this->selectedBatchId = null;
            $this->reset(['selectedProduct', 'productSearch', 'batch_code', 'harga_beli', 'harga_jual', 'margin', 'unit_id', 'availableUnits', 'master_harga_beli', 'master_harga_jual', 'qty_masuk']);
            $this->batch_code = $service->generateBatchCode();
            $this->tanggal_masuk = date('Y-m-d');
        }
        $this->showModal = true;
    }

    public function updateMasterPrices()
    {
        $this->validate([
            'selectedProduct' => 'required',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
        ]);

        $product = Product::findOrFail($this->selectedProduct);
        
        // Capture OLD state before update
        $oldState = [
            'harga_beli_default' => $product->harga_beli_default,
            'harga_jual_default' => $product->harga_jual_default,
        ];
        $oldUpdatedAt = $product->updated_at;

        // Perform Update
        $product->update([
            'harga_beli_default' => $this->harga_beli,
            'harga_jual_default' => $this->harga_jual,
        ]);
        
        $product->refresh();
        
        // Capture NEW state
        $newState = [
            'harga_beli_default' => $product->harga_beli_default,
            'harga_jual_default' => $product->harga_jual_default,
        ];
        
        // Calculate diff
        $changesOld = [];
        $changesNew = [];
        
        foreach ($oldState as $key => $value) {
            if ($value != $newState[$key]) {
                $changesOld[$key] = $value;
                $changesNew[$key] = $newState[$key];
            }
        }
        
        // Only log if there are changes
        if (!empty($changesOld)) {
            // Add timestamps (as requested format)
            $changesOld['updated_at'] = $oldUpdatedAt;
            $changesNew['updated_at'] = $product->updated_at;

            \App\Models\ProductLog::create([
                'product_id' => $product->id,
                'user_id' => auth()->id(),
                'action' => 'updated',
                'description' => 'Update harga dari Batch Form',
                'changes' => [
                    'old' => $changesOld,
                    'new' => $changesNew,
                ]
            ]);
        }

        $this->master_harga_beli = $this->harga_beli;
        $this->master_harga_jual = $this->harga_jual;
        $this->showConfirmMasterUpdate = false;

        $this->dispatch('toast', ['type' => 'success', 'message' => 'Harga Master Produk berhasil diperbarui']);
    }

    public function selectProduct($productId)
    {
        $product = Product::with('units')->find($productId);
        if ($product) {
            $this->selectedProduct = $product->id;
            $this->productSearch = $product->nama;
            $this->availableUnits = $product->units;
            $this->unit_id = ''; // Default to base unit
            
            // Auto-fill from master
            $this->master_harga_beli = $product->harga_beli_default ?? 0;
            $this->master_harga_jual = $product->harga_jual_default ?? 0;
            
            $this->harga_beli = $this->master_harga_beli;
            $this->harga_jual = $this->master_harga_jual;
            $this->margin = $this->harga_jual - $this->harga_beli;
        }
    }

    public function updatedHargaBeli()
    {
        $this->calculateHargaJual();
    }

    public function updatedMargin()
    {
        $this->calculateHargaJual();
    }

    public function calculateHargaJual()
    {
        $this->isCalculating = true;
        $this->harga_jual = (int)($this->harga_beli + $this->margin);
        $this->isCalculating = false;
    }

    public function updatedHargaJual()
    {
        $this->calculateMargin();
    }

    public function calculateMargin()
    {
        $this->margin = (int)((float)$this->harga_jual - (float)$this->harga_beli);
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function store()
    {
        $this->validate([
            'selectedProduct' => 'required',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'qty_masuk' => 'required|integer|min:1',
            'tanggal_masuk' => 'required|date',
        ]);

        try {
            $service = app(\App\Services\ProductBatchService::class);
            $data = [
                'product_id' => $this->selectedProduct,
                'unit_id' => $this->unit_id,
                'qty_input' => $this->qty_masuk,
                'harga_beli_per_base' => $this->harga_beli,
                'harga_jual_per_base' => $this->harga_jual,
                'tanggal_masuk' => $this->tanggal_masuk,
                'batch_code' => $this->batch_code, // Though service regenerates or uses this
            ];

            if ($this->isEdit) {
                $service->updateBatch($this->selectedBatchId, $data);
                $message = 'Batch berhasil diperbarui';
            } else {
                $service->createBatch($data);
                $message = 'Batch berhasil ditambahkan';
            }

            $this->closeModal();
            $this->dispatch('toast', ['type' => 'success', 'message' => $message]);
        } catch (\Exception $e) {
            $this->dispatch('toast', ['type' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function openDetailModal(int $id)
    {
        $this->batchDetail = ProductBatch::with('product')->findOrFail($id);
        $this->showDetailModal = true;
    }

    // History Modal
    public $showHistoryModal = false;
    public $batchLogs = [];

    public function openHistoryModal(int $batchId)
    {
        $this->batchLogs = \App\Models\ProductBatchLog::with('user')
            ->where('product_batch_id', $batchId)
            ->latest()
            ->get();
        $this->showHistoryModal = true;
    }

    public function closeHistoryModal()
    {
        $this->showHistoryModal = false;
        $this->batchLogs = [];
    }

    public function confirmDelete(int $id)
    {
        $this->selectedBatchId = $id;
        $batch = ProductBatch::findOrFail($id);
        
        // Pre-check FIFO rule to disable button if needed, 
        // but service handles the hard rejection.
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        try {
            $service = app(\App\Services\ProductBatchService::class);
            $service->deleteBatch($this->selectedBatchId);
            
            $this->showDeleteModal = false;
            $this->dispatch('toast', ['type' => 'success', 'message' => 'Batch berhasil dihapus']);
        } catch (\Exception $e) {
            $this->dispatch('toast', ['type' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function getFilteredProductsProperty()
    {
        $search = trim($this->productSearch);
        if (strlen($search) < 2) {
            return [];
        }

        return Product::where('is_active', true)
            ->where(function($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                  ->orWhere('kode_produk', 'like', '%' . $search . '%');
            })
            ->limit(5)
            ->get();
    }

    public function render()
    {
        $query = ProductBatch::with('product')
            ->when($this->search, fn($q) => $q->where('batch_code', 'like', '%' . $this->search . '%'))
            ->when($this->product_filter !== 'all', fn($q) => $q->where('product_id', $this->product_filter))
            ->orderBy('tanggal_masuk', 'desc');

        return view('livewire.product.product-batch-list', [
            'batches' => $query->paginate($this->perPage),
            'products' => Product::where('is_active', true)->orderBy('nama')->get(),
            'searchProducts' => $this->filteredProducts,
        ]);
    }
}
