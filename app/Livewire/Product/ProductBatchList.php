<?php

namespace App\Livewire\Product;

use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\ProductCategory;
use App\Services\ProductService;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ProductBatchList extends Component
{
    use WithPagination;

    // Filters & Search
    public $search = '';
    public $product_filter = 'all';
    public $perPage = 10;
    public $sortField = 'tanggal_masuk';
    public $sortDirection = 'desc';

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
    public $is_bonus = false;
    public $bonus_note = '';
    public $currentMultiplier = 1;
    
    // Master Product Reference (for comparison)
    public $master_harga_beli = 0;
    public $master_harga_jual = 0;
    public $showConfirmMasterUpdate = false;

    // Derived Data
    public $batchDetail = null; // Used for Detail Modal
    public $availableUnits = [];
    public $isCalculating = false;
    public $pendingEnter = false;

    // Quick Product Modal State
    public $showQuickProductModal = false;
    public $quick_nama = '';
    public $quick_category_name = '';
    public $quick_kode_produk = '';
    public $quick_harga_beli = 0;
    public $quick_margin = 0;
    public $quick_harga_jual = 0;
    public $quick_base_unit = 'Pcs';
    public $quick_units = []; // Array of ['label' => '', 'multiplier' => '']

    protected $queryString = [
        'search' => ['except' => ''],
        'product_filter' => ['except' => 'all'],
        'sortField' => ['except' => 'tanggal_masuk'],
        'sortDirection' => ['except' => 'desc'],
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

            $this->qty_masuk = (float)($batch->qty_masuk_original ?? $batch->qty_masuk_base);
            $this->tanggal_masuk = \Carbon\Carbon::parse($batch->tanggal_masuk)->format('Y-m-d');
            $this->is_bonus = $batch->is_bonus;
            $this->bonus_note = $batch->bonus_note;
            $this->availableUnits = $batch->product->units;

            // Try to match unit_id from input_unit_name
            $this->unit_id = '';
            if ($batch->input_unit_name && $batch->input_unit_name !== $batch->product->base_unit) {
                $units = collect($this->availableUnits);
                $matchedUnit = $units->where('label', $batch->input_unit_name)->first();
                if ($matchedUnit) {
                    $this->unit_id = is_array($matchedUnit) ? $matchedUnit['id'] : $matchedUnit->id;
                    $this->currentMultiplier = is_array($matchedUnit) ? $matchedUnit['multiplier'] : $matchedUnit->multiplier;
                }
            }
        } else {
            $this->isEdit = false;
            $this->selectedBatchId = null;
            $this->reset(['selectedProduct', 'productSearch', 'batch_code', 'harga_beli', 'harga_jual', 'margin', 'unit_id', 'availableUnits', 'master_harga_beli', 'master_harga_jual', 'qty_masuk', 'is_bonus', 'bonus_note', 'currentMultiplier']);
            $this->currentMultiplier = 1;
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
                'user_id' => \Illuminate\Support\Facades\Auth::id(),
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
            $this->currentMultiplier = 1;
            
            // Auto-fill from master
            $this->master_harga_beli = $product->harga_beli_default ?? 0;
            $this->master_harga_jual = $product->harga_jual_default ?? 0;
            
            $this->harga_beli = $this->master_harga_beli;
            $this->harga_jual = $this->master_harga_jual;

            // Try to match unit price if master has units
            if ($this->unit_id) {
                $unit = collect($this->availableUnits)->where('id', $this->unit_id)->first();
                if ($unit && $unit->harga_beli) {
                    $this->harga_beli = $unit->harga_beli;
                    $this->harga_jual = $unit->harga_jual ?? ($this->master_harga_jual * $unit->multiplier);
                }
            }

            $this->margin = $this->harga_jual - $this->harga_beli;
        }
    }

    public function updatedUnitId($value)
    {
        if ($value && $this->selectedProduct) {
            $unit = collect($this->availableUnits)->where('id', $value)->first();
            if ($unit) {
                $this->currentMultiplier = is_array($unit) ? $unit['multiplier'] : $unit->multiplier;
                if ($unit->harga_beli) {
                    $this->harga_beli = $unit->harga_beli;
                    $this->harga_jual = $unit->harga_jual ?? ($this->master_harga_jual * $this->currentMultiplier);
                    $this->calculateMargin();
                }
            }
        } else {
            $this->currentMultiplier = 1;
        }
    }

    public function selectFirstResult()
    {
        $results = $this->filteredProducts;
        if (count($results) > 0) {
            $this->selectProduct($results[0]->id);
            $this->pendingEnter = false;
        } else {
            $this->pendingEnter = true;
        }
    }

    public function updatedProductSearch()
    {
        if ($this->pendingEnter) {
            $results = $this->filteredProducts;
            if (count($results) >= 1) {
                // If we have results and were waiting for them, pick the first one
                $this->selectProduct($results[0]->id);
                $this->pendingEnter = false;
            }
        }
    }

    public function updatedHargaBeli()
    {
        if ($this->is_bonus) {
            $this->harga_beli = 0;
        }
        $this->calculateHargaJual();
    }

    public function updatedIsBonus($value)
    {
        if ($value) {
            $this->harga_beli = 0;
            $this->margin = $this->harga_jual; // If bonus, margin = selling price
        } else {
            $this->harga_beli = $this->master_harga_beli;
            $this->margin = $this->harga_jual - $this->harga_beli;
        }
    }

    public function updatedMargin()
    {
        $this->calculateHargaJual();
    }

    public function calculateHargaJual()
    {
        $this->isCalculating = true;
        // Cast to float to prevent TypeError: string + int
        $this->harga_jual = (int)((float)$this->harga_beli + (float)$this->margin);
        $this->isCalculating = false;
    }

    public function updatedHargaJual()
    {
        $this->calculateMargin();
    }

    public function calculateMargin()
    {
        // Cast to float to prevent TypeError: string - float
        $this->margin = (int)((float)$this->harga_jual - (float)$this->harga_beli);
    }

    // --- Quick Product Methods ---
    public function openQuickProductModal()
    {
        $this->reset(['quick_nama', 'quick_category_name', 'quick_kode_produk', 'quick_harga_beli', 'quick_margin', 'quick_harga_jual', 'quick_base_unit', 'quick_units']);
        $this->quick_base_unit = 'Pcs';
        // Auto-fill barcode if something was searched
        if ($this->productSearch && strlen($this->productSearch) > 3 && !is_numeric($this->productSearch) === false) {
             $this->quick_kode_produk = $this->productSearch;
        }
        $this->showQuickProductModal = true;
    }

    public function updatedQuickHargaBeli() { $this->calculateQuickHargaJual(); }
    public function updatedQuickMargin() { $this->calculateQuickHargaJual(); }
    public function updatedQuickHargaJual() { $this->calculateQuickMargin(); }

    private function calculateQuickHargaJual()
    {
        $this->quick_harga_jual = (float)$this->quick_harga_beli + (float)$this->quick_margin;
    }

    private function calculateQuickMargin()
    {
        $this->quick_margin = (float)$this->quick_harga_jual - (float)$this->quick_harga_beli;
        $this->updateQuickUnitsPrices();
    }

    private function updateQuickUnitsPrices()
    {
        foreach ($this->quick_units as $index => $unit) {
            if (!$unit['is_nonlinear']) {
                $this->quick_units[$index]['harga_jual'] = (float)$this->quick_harga_jual * (float)$unit['multiplier'];
                $this->quick_units[$index]['harga_beli'] = (float)$this->quick_harga_beli * (float)$unit['multiplier'];
            }
        }
    }

    public function updatedQuickUnits($value, $key)
    {
        // $key will be something like '0.multiplier' or '0.is_nonlinear'
        if (str_contains($key, 'multiplier') || str_contains($key, 'is_nonlinear')) {
            $parts = explode('.', $key);
            $index = $parts[0];

            // If nonlinear is just turned on or multiplier changed, pre-fill prices
            if ($this->quick_units[$index]['is_nonlinear']) {
                if (is_null($this->quick_units[$index]['harga_jual']) || $this->quick_units[$index]['harga_jual'] == 0) {
                    $this->quick_units[$index]['harga_jual'] = (float)$this->quick_harga_jual * (float)$this->quick_units[$index]['multiplier'];
                }
                if (is_null($this->quick_units[$index]['harga_beli']) || $this->quick_units[$index]['harga_beli'] == 0) {
                    $this->quick_units[$index]['harga_beli'] = (float)$this->quick_harga_beli * (float)$this->quick_units[$index]['multiplier'];
                }
            } else {
                $this->quick_units[$index]['harga_jual'] = (float)$this->quick_harga_jual * (float)$this->quick_units[$index]['multiplier'];
                $this->quick_units[$index]['harga_beli'] = (float)$this->quick_harga_beli * (float)$this->quick_units[$index]['multiplier'];
            }
        }
    }

    public function generateQuickBarcode(ProductService $service)
    {
        $prefix = 'PRD-';
        $random = str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
        $barcode = $prefix . $random;
        if (!$service->isBarcodeUnique($barcode)) {
            $random = str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
            $barcode = $prefix . $random;
        }
        $this->quick_kode_produk = $barcode;
    }

    public function addQuickUnit()
    {
        $this->quick_units[] = [
            'label' => '',
            'multiplier' => 1,
            'harga_jual' => null,
            'harga_beli' => null,
            'is_nonlinear' => false
        ];
    }

    public function removeQuickUnit($index)
    {
        unset($this->quick_units[$index]);
        $this->quick_units = array_values($this->quick_units);
    }

    public function storeQuickProduct(ProductService $service)
    {
        Log::info('storeQuickProduct: Method called');
        $this->resetValidation();

        try {
            Log::info('storeQuickProduct: Starting validation');
            $this->validate([
                'quick_nama' => 'required|min:3',
                'quick_category_name' => 'required|min:2',
                'quick_kode_produk' => 'required',
                'quick_harga_beli' => 'required|numeric|min:0',
                'quick_harga_jual' => 'required|numeric|min:0',
                'quick_base_unit' => 'required',
                'quick_units.*.label' => 'sometimes|required',
                'quick_units.*.multiplier' => 'sometimes|required|numeric|min:1',
                'quick_units.*.harga_jual' => 'nullable|numeric|min:0',
                'quick_units.*.harga_beli' => 'nullable|numeric|min:0',
            ], [
                'quick_units.*.label.required' => 'Label satuan harus diisi',
                'quick_units.*.multiplier.required' => 'Pengali harus diisi',
                'quick_units.*.multiplier.min' => 'Pengali minimal 1',
            ]);
            Log::info('storeQuickProduct: Validation passed');

            $this->dispatch('toast', ['type' => 'info', 'message' => 'Sedang memproses...']);

            // Find or Create Category
            $category = ProductCategory::where('name', 'like', trim($this->quick_category_name))->first();

            if ($category) {
                Log::info('storeQuickProduct: Using existing category', ['name' => $category->name]);
            } else {
                Log::info('storeQuickProduct: Creating new category', ['name' => $this->quick_category_name]);
                $category = ProductCategory::create([
                    'name' => trim($this->quick_category_name),
                    'is_active' => true
                ]);

                \App\Models\ProductCategoryLog::create([
                    'category_id' => $category->id,
                    'action' => 'create',
                    'new_value' => $category->name,
                ]);
            }

            $data = [
                'nama' => $this->quick_nama,
                'category_id' => $category->id,
                'kode_produk' => $this->quick_kode_produk,
                'harga_beli_default' => $this->quick_harga_beli,
                'harga_jual_default' => $this->quick_harga_jual,
                'base_unit' => $this->quick_base_unit,
                'is_active' => true,
                'units' => $this->quick_units
            ];

            Log::info('storeQuickProduct: Calling service createProduct', ['data' => $data]);
            $product = $service->createProduct($data);
            Log::info('storeQuickProduct: Product created successfully', ['id' => $product->id]);
            
            $this->showQuickProductModal = false;
            $this->selectProduct($product->id);
            Log::info('storeQuickProduct: selectProduct called');
            $this->dispatch('toast', ['type' => 'success', 'message' => 'Produk Master baru berhasil dibuat']);
        } catch (ValidationException $e) {
            Log::warning('storeQuickProduct: Validation failed', ['errors' => $e->errors()]);
            throw $e;
        } catch (\Throwable $e) {
            Log::error('storeQuickProduct: CRITICAL FAILURE', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Gagal: ' . $e->getMessage()]);
        }
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
        ], [
            'qty_masuk.min' => 'Qty harus lebih dari 0',
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
                'batch_code' => $this->batch_code,
                'is_bonus' => $this->is_bonus,
                'bonus_note' => $this->bonus_note,
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
            $this->showModal = false;
            $this->dispatch('toast', ['type' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
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
            $this->showDeleteModal = false;
            $this->dispatch('toast', ['type' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function getFilteredProductsProperty()
    {
        $search = trim($this->productSearch);
        if (strlen($search) < 3) {
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
        $query = ProductBatch::with(['product', 'adjustmentItems.adjustment.user'])
            ->when($this->search, function ($q) {
                $search = '%' . $this->search . '%';
                $q->where('batch_code', 'like', $search)
                    ->orWhereHas('product', function ($pq) use ($search) {
                        $pq->where('nama', 'like', $search)
                            ->orWhere('kode_produk', 'like', $search);
                    });
            })
            ->when($this->product_filter !== 'all', fn($q) => $q->where('product_id', $this->product_filter))
            ->orderBy($this->sortField, $this->sortDirection)
            ->orderBy('created_at', 'desc');

        return view('livewire.product.product-batch-list', [
            'batches' => $query->paginate($this->perPage),
            'products' => Product::where('is_active', true)->orderBy('nama')->get(),
            'searchProducts' => $this->filteredProducts,
            'categories' => ProductCategory::where('is_active', true)->orderBy('name')->get(),
        ]);
    }
}
