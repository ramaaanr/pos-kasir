<?php

namespace App\Services;

use App\Models\ProductCategory;
use App\Models\ProductCategoryLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ProductCategoryService
{
    /**
     * Get paginated and filtered product categories.
     */
    public function getPaginatedCategories(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return ProductCategory::query()
            ->withCount('products')
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->search($search);
            })
            ->when($filters['status'] ?? null, function ($query, $status) {
                if ($status === 'active') {
                    $query->active();
                } elseif ($status === 'inactive') {
                    $query->where('is_active', false);
                }
            })
            ->when($filters['sort_by'] ?? null, function ($query, $sortBy) use ($filters) {
                $direction = $filters['sort_direction'] ?? 'asc';
                $query->orderBy($sortBy, $direction);
            }, function ($query) {
                $query->latest();
            })
            ->paginate($perPage);
    }

    /**
     * Get all active categories.
     */
    public function getActiveCategories()
    {
        return ProductCategory::active()->orderBy('name')->get();
    }

    /**
     * Get summary statistics for categories.
     */
    public function getCategorySummary(): array
    {
        return [
            'total' => ProductCategory::count(),
            'active' => ProductCategory::active()->count(),
            'inactive' => ProductCategory::where('is_active', false)->count(),
        ];
    }

    /**
     * Create a new product category.
     *
     * @throws \Exception
     */
    public function create(array $data): ProductCategory
    {
        return DB::transaction(function () use ($data) {
            $exists = ProductCategory::where('name', $data['name'])->exists();

            if ($exists) {
                throw new \Exception('Nama kategori sudah digunakan');
            }

            $category = ProductCategory::create([
                'name' => $data['name'],
                'is_active' => true,
            ]);

            ProductCategoryLog::create([
                'category_id' => $category->id,
                'action' => 'create',
                'new_value' => $category->name,
            ]);

            return $category;
        });
    }

    /**
     * Update a product category.
     *
     * @throws \Exception
     */
    public function update(int $id, array $data): ProductCategory
    {
        return DB::transaction(function () use ($id, $data) {
            $category = ProductCategory::findOrFail($id);
            $oldName = $category->name;

            $exists = ProductCategory::where('name', $data['name'])
                ->where('id', '!=', $id)
                ->exists();

            if ($exists) {
                throw new \Exception('Nama kategori sudah digunakan');
            }

            $category->update([
                'name' => $data['name'],
            ]);

            if ($oldName !== $category->name) {
                ProductCategoryLog::create([
                    'category_id' => $category->id,
                    'action' => 'update',
                    'old_value' => $oldName,
                    'new_value' => $category->name,
                ]);
            }

            return $category;
        });
    }

    /**
     * Toggle the status of a category.
     */
    public function toggleStatus(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $category = ProductCategory::findOrFail($id);
            $oldStatus = $category->is_active ? 'Aktif' : 'Nonaktif';
            
            $category->update([
                'is_active' => !$category->is_active
            ]);

            $newStatus = $category->is_active ? 'Aktif' : 'Nonaktif';

            ProductCategoryLog::create([
                'category_id' => $category->id,
                'action' => 'update_status',
                'old_value' => $oldStatus,
                'new_value' => $newStatus,
            ]);

            return true;
        });
    }

    /**
     * Delete a category.
     */
    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $category = ProductCategory::findOrFail($id);
            
            ProductCategoryLog::create([
                'category_id' => $category->id,
                'action' => 'delete',
                'old_value' => $category->name,
            ]);

            $category->delete();
            return true;
        });
    }
}
