<?php

namespace App\Livewire\Product;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Services\ProductService;
use App\Services\ProductCategoryService;
use Livewire\Component;
use Livewire\WithPagination;

class ProductList extends Component
{
    use WithPagination;

    // Filters & Search
    public $search = '';
    public $category_id = 'all';
    public $status = 'all';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;

    // Modal State
    public $showModal = false;
    public $showHistoryModal = false;
    public $showDeleteModal = false;
    public $showDetailModal = false;
    public $selectedProductId = null;
    public $logs = [];
    public $isEdit = false;

    // Form Data
    public $nama = '';
    public $category_name = '';
    public $kode_produk = '';
    public $harga_beli = 0;
    public $margin = 0;
    public $harga_jual = 0;
    public $base_unit = 'Pcs';
    public $is_active = true;
    public $units = []; // Array of ['label' => '', 'multiplier' => '']
    public $selectedProduct = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'category_id' => ['except' => 'all'],
        'status' => ['except' => 'all'],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function updatedHargaBeli()
    {
        $this->calculateHargaJual();
    }

    public function updatedMargin()
    {
        $this->calculateHargaJual();
    }

    public function updatedHargaJual()
    {
        $this->calculateMargin();
    }

    public function sort($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    private function calculateHargaJual()
    {
        $this->harga_jual = (float)$this->harga_beli + (float)$this->margin;
    }

    private function calculateMargin()
    {
        $this->margin = (float)$this->harga_jual - (float)$this->harga_beli;
    }

    public function generateBarcode(ProductService $service)
    {
        $prefix = 'PRD-';
        $random = str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
        $barcode = $prefix . $random;

        // Simple check if unique, if not try again once
        if (!$service->isBarcodeUnique($barcode)) {
            $random = str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
            $barcode = $prefix . $random;
        }

        $this->kode_produk = $barcode;
    }

    public function openModal(?int $id = null, ?ProductService $service = null)
    {
        $this->resetValidation();
        if ($id) {
            $this->isEdit = true;
            $this->selectedProductId = $id;
            $product = Product::with('category')->findOrFail($id);
            $this->nama = $product->nama;
            $this->category_name = $product->category->name ?? '';
            $this->kode_produk = $product->kode_produk;
            $this->harga_beli = $product->harga_beli_default;
            $this->harga_jual = $product->harga_jual_default;
            $this->margin = (float)$this->harga_jual - (float)$this->harga_beli;
            $this->base_unit = $product->base_unit;
            $this->is_active = $product->is_active;
            $this->units = $product->units->map(fn($u) => ['label' => $u->label, 'multiplier' => $u->multiplier])->toArray();
        } else {
            $this->isEdit = false;
            $this->selectedProductId = null;
            $this->reset(['nama', 'category_name', 'kode_produk', 'harga_beli', 'margin', 'harga_jual', 'base_unit', 'is_active', 'units']);
            $this->base_unit = 'Pcs';
            $this->is_active = true;
            $this->units = [];
        }
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function store(ProductService $service)
    {
        $this->validate([
            'nama' => 'required|min:3',
            'category_name' => 'required|min:2',
            'kode_produk' => 'required',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'base_unit' => 'required',
        ]);

        try {
            // Find or Create Category
            $category = ProductCategory::where('name', 'like', trim($this->category_name))->first();

            if ($category) {
                if (!$this->isEdit || ($this->selectedProductId && Product::find($this->selectedProductId)->category_id !== $category->id)) {
                    $this->dispatch('toast', ['type' => 'info', 'message' => "Menggunakan kategori existing: {$category->name}"]);
                }
            } else {
                $category = ProductCategory::create([
                    'name' => trim($this->category_name),
                    'is_active' => true
                ]);
                $this->dispatch('toast', ['type' => 'success', 'message' => "Kategori baru '{$category->name}' berhasil dibuat"]);

                \App\Models\ProductCategoryLog::create([
                    'category_id' => $category->id,
                    'action' => 'create',
                    'new_value' => $category->name,
                ]);
            }

            $data = [
                'nama' => $this->nama,
                'category_id' => $category->id,
                'kode_produk' => $this->kode_produk,
                'base_unit' => $this->base_unit,
                'harga_beli_default' => $this->harga_beli,
                'harga_jual_default' => $this->harga_jual,
                'is_active' => $this->is_active,
                'units' => $this->units,
            ];

            if ($this->isEdit) {
                $service->updateProduct($this->selectedProductId, $data);
                $message = 'Produk berhasil diperbarui';
            } else {
                $service->createProduct($data);
                $message = 'Produk berhasil ditambahkan';
            }

            $this->closeModal();
            $this->dispatch('toast', ['type' => 'success', 'message' => $message]);
        } catch (\Exception $e) {
            $this->dispatch('toast', ['type' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function addUnit()
    {
        $this->units[] = ['label' => '', 'multiplier' => ''];
    }

    public function removeUnit($index)
    {
        unset($this->units[$index]);
        $this->units = array_values($this->units);
    }

    public function openHistoryModal(int $id, ProductService $service)
    {
        $this->selectedProductId = $id;
        $this->logs = $service->getProductLogs($id);
        $this->showHistoryModal = true;
    }

    public function closeHistoryModal()
    {
        $this->showHistoryModal = false;
        $this->logs = [];
    }

    public function openDetailModal(int $id)
    {
        $this->selectedProduct = \App\Models\Product::with(['category', 'units'])
            ->withSum('batches', 'qty_sisa_base')
            ->findOrFail($id);
        $this->showDetailModal = true;
    }

    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->selectedProduct = null;
    }

    public function toggleStatus(int $id, ProductService $service)
    {
        try {
            $service->toggleStatus($id);
            $this->dispatch('toast', ['type' => 'success', 'message' => 'Status produk berhasil diperbarui']);
        } catch (\Exception $e) {
            $this->dispatch('toast', ['type' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function confirmDelete(int $id)
    {
        $this->selectedProductId = $id;
        $this->showDeleteModal = true;
    }

    public function delete(ProductService $service)
    {
        try {
            $service->deleteProduct($this->selectedProductId);
            $this->showDeleteModal = false;
            $this->selectedProductId = null;
            $this->dispatch('toast', ['type' => 'success', 'message' => 'Produk berhasil dihapus']);
        } catch (\Exception $e) {
            $this->showDeleteModal = false;
            $this->selectedProductId = null;
            $this->dispatch('toast', ['type' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function render(ProductService $productService, ProductCategoryService $categoryService)
    {
        $filters = [
            'search' => $this->search,
            'category_id' => $this->category_id,
            'status' => $this->status,
            'sort_by' => $this->sortBy,
            'sort_direction' => $this->sortDirection,
        ];

        return view('livewire.product.product-list', [
            'products' => $productService->getPaginatedProducts($filters, $this->perPage),
            'categories' => $categoryService->getActiveCategories(),
        ]);
    }
}
