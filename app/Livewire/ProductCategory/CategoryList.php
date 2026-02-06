<?php

namespace App\Livewire\ProductCategory;

use App\Services\ProductCategoryService;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ProductCategory;

class CategoryList extends Component
{
    use WithPagination;

    public $search = '';
    public $status = 'all';
    public $sortBy = 'name';
    public $sortDirection = 'asc';

    // Form Properties
    public $showModal = false;
    public $selectedCategoryId = null;
    public $name = '';
    public $showDeleteModal = false;
    public $categoryToDelete = null;

    protected $rules = [
        'name' => 'required|max:50',
    ];

    protected $messages = [
        'name.required' => 'Nama kategori wajib diisi',
        'name.max' => 'Maksimal 50 karakter',
    ];

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => 'all'],
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatus()
    {
        $this->resetPage();
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

    public function toggleStatus($id, ProductCategoryService $service)
    {
        $service->toggleStatus($id);
    }

    public function confirmDelete($id)
    {
        $this->categoryToDelete = ProductCategory::findOrFail($id);
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->categoryToDelete = null;
    }

    public function delete(ProductCategoryService $service)
    {
        if (!$this->categoryToDelete) {
            return;
        }

        try {
            $service->delete($this->categoryToDelete->id);
            $this->closeDeleteModal();
            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Kategori berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function openModal()
    {
        $this->resetErrorBag();
        $this->name = '';
        $this->selectedCategoryId = null;
        $this->showModal = true;
    }

    public function edit($id)
    {
        $category = ProductCategory::findOrFail($id);
        $this->resetErrorBag();
        $this->selectedCategoryId = $id;
        $this->name = $category->name;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedCategoryId = null;
    }

    public function save(ProductCategoryService $service)
    {
        $this->validate();

        try {
            if ($this->selectedCategoryId) {
                $service->update($this->selectedCategoryId, ['name' => $this->name]);
                $message = 'Kategori berhasil diperbarui';
            } else {
                $service->create(['name' => $this->name]);
                $message = 'Kategori berhasil ditambahkan';
            }

            $this->closeModal();
            $this->dispatch('toast', [
                'type' => 'success',
                'message' => $message
            ]);
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function render(ProductCategoryService $service)
    {
        $categories = $service->getPaginatedCategories([
            'search' => $this->search,
            'status' => $this->status,
            'sort_by' => $this->sortBy,
            'sort_direction' => $this->sortDirection,
        ], 10);

        $summary = $service->getCategorySummary();

        return view('livewire.product-category.category-list', [
            'categories' => $categories,
            'summary' => $summary,
        ]);
    }
}
