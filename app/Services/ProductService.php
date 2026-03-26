<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProductService
{
    /**
     * Get paginated and filtered products.
     */
    public function getPaginatedProducts(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return Product::query()
            ->with(['category', 'adjustmentItems' => function ($q) {
                $q->with(['adjustment', 'batch'])->latest()->take(3);
            }])
            ->withSum('batches', 'qty_sisa_base')
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->search($search);
            })
            ->when($filters['category_id'] ?? null, function ($query, $categoryId) {
                if ($categoryId !== 'all') {
                    $query->where('category_id', $categoryId);
                }
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
                
                if ($sortBy === 'category_name') {
                    $query->join('product_categories', 'products.category_id', '=', 'product_categories.id')
                        ->orderBy('product_categories.name', $direction)
                        ->select('products.*');
                } elseif ($sortBy === 'stok') {
                    $query->orderBy('batches_sum_qty_sisa_base', $direction);
                } else {
                    $query->orderBy($sortBy, $direction);
                }
            }, function ($query) {
                $query->latest();
            })
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Create a new product.
     *
     * @throws \Exception
     */
    public function createProduct(array $data): Product
    {
        Log::info('ProductService: createProduct started', ['nama' => $data['nama'] ?? 'N/A']);
        return DB::transaction(function () use ($data) {
            Log::info('ProductService: In transaction');
            if (!$this->isBarcodeUnique($data['kode_produk'])) {
                Log::warning('ProductService: Barcode not unique', ['barcode' => $data['kode_produk']]);
                $existingProduct = Product::where('kode_produk', $data['kode_produk'])->first();
                throw new \Exception("Barcode sudah digunakan oleh produk: " . $existingProduct->nama);
            }

            Log::info('ProductService: Creating product record');
            $product = Product::create([
                'category_id' => $data['category_id'],
                'kode_produk' => $data['kode_produk'],
                'nama' => $data['nama'],
                'base_unit' => $data['base_unit'] ?? 'Pcs',
                'harga_beli_default' => $data['harga_beli_default'],
                'harga_jual_default' => $data['harga_jual_default'],
                'is_active' => $data['is_active'] ?? true,
            ]);
            Log::info('ProductService: Product record created', ['id' => $product->id]);

            ProductLog::create([
                'product_id' => $product->id,
                'user_id' => Auth::id(),
                'action' => 'created',
                'description' => 'Produk baru ditambahkan ke sistem',
            ]);

            // Handle units
            if (!empty($data['units']) && is_array($data['units'])) {
                Log::info('ProductService: Creating units', ['count' => count($data['units'])]);
                foreach ($data['units'] as $unit) {
                    if (!empty($unit['label']) && !empty($unit['multiplier'])) {
                        Log::info('ProductService: Adding unit', ['label' => $unit['label']]);
                        $product->units()->create([
                            'label' => $unit['label'],
                            'multiplier' => $unit['multiplier'],
                            'harga_jual' => $unit['harga_jual'] ?? null,
                            'harga_beli' => $unit['harga_beli'] ?? null,
                        ]);
                    }
                }
            }

            Log::info('ProductService: createProduct finished successfully');
            return $product;
        });
    }

    /**
     * Update an existing product.
     *
     * @throws \Exception
     */
    public function updateProduct(int $id, array $data): Product
    {
        return DB::transaction(function () use ($id, $data) {
            $product = Product::findOrFail($id);

            if (!$this->isBarcodeUnique($data['kode_produk'], $id)) {
                $existingProduct = Product::where('kode_produk', $data['kode_produk'])->where('id', '!=', $id)->first();
                throw new \Exception("Barcode sudah digunakan oleh produk lain: " . $existingProduct->nama);
            }

            $oldData = $product->toArray();
            
            $product->update([
                'category_id' => $data['category_id'],
                'kode_produk' => $data['kode_produk'],
                'nama' => $data['nama'],
                'base_unit' => $data['base_unit'] ?? 'Pcs',
                'harga_beli_default' => $data['harga_beli_default'],
                'harga_jual_default' => $data['harga_jual_default'],
                'is_active' => $data['is_active'],
            ]);

            if ($product->wasChanged()) {
                $changes = array_intersect_key($oldData, $product->getChanges());
                
                // Resolve category name if category_id changed
                if (isset($changes['category_id'])) {
                    $oldCategory = \App\Models\ProductCategory::find($changes['category_id']);
                    $changes['category_id'] = $oldCategory ? $oldCategory->name : 'N/A';
                }

                $fieldNames = [
                    'nama' => 'Nama',
                    'category_id' => 'Kategori',
                    'kode_produk' => 'Barcode',
                    'base_unit' => 'Satuan',
                    'harga_beli_default' => 'Harga Beli',
                    'harga_jual_default' => 'Harga Jual',
                    'is_active' => 'Status',
                ];
                
                $changedFields = [];
                foreach (array_keys($product->getChanges()) as $field) {
                    if (isset($fieldNames[$field])) {
                        $changedFields[] = $fieldNames[$field];
                    }
                }
                
                $description = 'Detail produk diperbarui' . (count($changedFields) > 0 ? ': ' . implode(', ', $changedFields) : '');

                $newValues = array_intersect_key($product->getAttributes(), $product->getChanges());
                if (isset($newValues['category_id'])) {
                    $newCategory = \App\Models\ProductCategory::find($newValues['category_id']);
                    $newValues['category_id'] = $newCategory ? $newCategory->name : 'N/A';
                }

                ProductLog::create([
                    'product_id' => $product->id,
                    'user_id' => Auth::id(),
                    'action' => 'updated',
                    'description' => $description,
                    'changes' => [
                        'old' => $changes,
                        'new' => $newValues
                    ],
                ]);
            }

            // Sync units
            if (isset($data['units']) && is_array($data['units'])) {
                $product->units()->delete();
                foreach ($data['units'] as $unit) {
                    if (!empty($unit['label']) && !empty($unit['multiplier'])) {
                        $product->units()->create([
                            'label' => $unit['label'],
                            'multiplier' => $unit['multiplier'],
                            'harga_jual' => $unit['harga_jual'] ?? null,
                            'harga_beli' => $unit['harga_beli'] ?? null,
                        ]);
                    }
                }
            }

            return $product;
        });
    }

    /**
     * Check if barcode is unique.
     */
    public function isBarcodeUnique(string $barcode, ?int $exceptId = null): bool
    {
        return !Product::where('kode_produk', $barcode)
            ->when($exceptId, function ($query, $id) {
                $query->where('id', '!=', $id);
            })
            ->exists();
    }

    /**
     * Toggle the active status of a product.
     */
    public function toggleStatus(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $product = Product::findOrFail($id);
            $newStatus = !$product->is_active;
            
            $updated = $product->update([
                'is_active' => $newStatus
            ]);

            if ($updated) {
                ProductLog::create([
                    'product_id' => $product->id,
                    'user_id' => Auth::id(),
                    'action' => 'toggled_status',
                    'description' => 'Status produk diubah menjadi ' . ($newStatus ? 'Aktif' : 'Nonaktif'),
                ]);
            }

            return $updated;
        });
    }

    /**
     * Get logs for a specific product.
     */
    public function getProductLogs(int $productId)
    {
        return ProductLog::where('product_id', $productId)
            ->with('user')
            ->latest()
            ->get();
    }

    /**
     * Delete a product (soft delete).
     */
    public function deleteProduct(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $product = Product::findOrFail($id);

            // Check if product has batches
            if ($product->batches()->exists()) {
                throw new \Exception("Produk tidak dapat dihapus karena sudah memiliki transaksi/batch");
            }

            ProductLog::create([
                'product_id' => $product->id,
                'user_id' => Auth::id(),
                'action' => 'deleted',
                'description' => 'Produk dihapus (Soft Delete)',
            ]);

            return $product->delete();
        });
    }
}
