<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\ProductUnit;
use Illuminate\Support\Facades\DB;

class ProductBatchService
{
    /**
     * Create a new product batch with unit conversion and validation.
     */
    public function createBatch(array $data)
    {
        return DB::transaction(function () use ($data) {
            $product = Product::findOrFail($data['product_id']);
            
            // Resolve multiplier from unit selection (or 1 if base)
            $multiplier = 1;
            if (isset($data['unit_id']) && $data['unit_id']) {
                $unit = ProductUnit::where('product_id', $product->id)->findOrFail($data['unit_id']);
                $multiplier = $unit->multiplier;
            }

            // Convert qty to base unit (Hard Constraint: Integer only)
            $qty_input = $data['qty_input'];
            
            if (!is_numeric($qty_input) || (float)$qty_input < 0) {
                throw new \Exception('Kuantitas tidak valid.');
            }

            $qty_masuk_base = (int)round($qty_input * $multiplier);

            // Generate Batch Code
            $batch_code = $this->generateBatchCode();

            $batch = ProductBatch::create([
                'product_id' => $product->id,
                'batch_code' => $batch_code,
                'harga_beli_per_unit' => $data['harga_beli_per_base'],
                'harga_jual_per_unit' => $data['harga_jual_per_base'],
                'qty_masuk_base' => $qty_masuk_base,
                'qty_sisa_base' => $qty_masuk_base,
                'tanggal_masuk' => $data['tanggal_masuk'] ?? now(),
            ]);

            // Log Creation
            \App\Models\ProductBatchLog::create([
                'product_batch_id' => $batch->id,
                'user_id' => auth()->id(),
                'action' => 'created',
                'qty_change' => $qty_masuk_base,
                'qty_before' => 0,
                'qty_after' => $qty_masuk_base,
                'description' => 'Batch created via Stok Masuk form',
            ]);

            return $batch;
        });
    }

    /**
     * Update an existing batch (restricted by FIFO rules).
     * Note: Per user spec, update is discouraged except via Stock Adjustment.
     * We keep this for now but enforce the hard constraint.
     */
    public function updateBatch(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $batch = ProductBatch::findOrFail($id);

            // Hard Constraint: Reject update if qty_sisa_base < qty_masuk_base
            if ($batch->qty_sisa_base < $batch->qty_masuk_base) {
                throw new \Exception('Batch tidak dapat diubah karena stok sudah mulai digunakan (FIFO Rule).');
            }

            $oldQty = $batch->qty_masuk_base;
            
            $product = Product::findOrFail($data['product_id']);
            
            $multiplier = 1;
            if (isset($data['unit_id']) && $data['unit_id']) {
                $unit = ProductUnit::where('product_id', $product->id)->findOrFail($data['unit_id']);
                $multiplier = $unit->multiplier;
            }

            $qty_input = $data['qty_input'];
            $qty_masuk_base = (int)round($qty_input * $multiplier);

            $batch->update([
                'product_id' => $product->id,
                'harga_beli_per_unit' => $data['harga_beli_per_base'],
                'harga_jual_per_unit' => $data['harga_jual_per_base'],
                'qty_masuk_base' => $qty_masuk_base,
                'qty_sisa_base' => $qty_masuk_base,
                'tanggal_masuk' => $data['tanggal_masuk'],
            ]);
            
            // Log Update if qty changed or just general update
            if ($oldQty !== $qty_masuk_base) {
                \App\Models\ProductBatchLog::create([
                    'product_batch_id' => $batch->id,
                    'user_id' => auth()->id(),
                    'action' => 'updated',
                    'qty_change' => $qty_masuk_base - $oldQty,
                    'qty_before' => $oldQty,
                    'qty_after' => $qty_masuk_base,
                    'description' => 'Batch quantity updated via Edit form',
                ]);
            } else {
                 \App\Models\ProductBatchLog::create([
                    'product_batch_id' => $batch->id,
                    'user_id' => auth()->id(),
                    'action' => 'updated',
                    'qty_change' => 0,
                    'qty_before' => $qty_masuk_base,
                    'qty_after' => $qty_masuk_base,
                    'description' => 'Batch details updated',
                ]);
            }

            return $batch;
        });
    }

    /**
     * Delete a batch (restricted by FIFO rules).
     */
    public function deleteBatch(int $id)
    {
        $batch = ProductBatch::findOrFail($id);

        if ($batch->qty_sisa_base < $batch->qty_masuk_base) {
            throw new \Exception('Batch tidak dapat dihapus karena stok sudah mulai digunakan (FIFO Rule).');
        }

        return $batch->delete();
    }

    /**
     * Generate a unique batch code.
     */
    public function generateBatchCode(): string
    {
        $date = now()->format('Ymd');
        $count = ProductBatch::whereDate('created_at', now()->toDateString())->count() + 1;
        $code = "BCH-{$date}-" . str_pad($count, 3, '0', STR_PAD_LEFT);

        // Ensure uniqueness
        while (ProductBatch::where('batch_code', $code)->exists()) {
            $count++;
            $code = "BCH-{$date}-" . str_pad($count, 3, '0', STR_PAD_LEFT);
        }

        return $code;
    }
}
