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
            $unitName = $product->base_unit;
            if (isset($data['unit_id']) && $data['unit_id']) {
                $unit = ProductUnit::where('product_id', $product->id)->findOrFail($data['unit_id']);
                $multiplier = $unit->multiplier;
                $unitName = $unit->label;
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
                'input_unit_name' => $unitName,
                'qty_masuk_original' => $qty_input,
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

            $multiplier = 1;
            $product = Product::findOrFail($data['product_id']);
            $unitName = $product->base_unit;
            if (isset($data['unit_id']) && $data['unit_id']) {
                $unit = ProductUnit::where('product_id', $product->id)->findOrFail($data['unit_id']);
                $multiplier = $unit->multiplier;
                $unitName = $unit->label;
            }

            $qty_input = $data['qty_input'];
            $qty_masuk_base = (int)round($qty_input * $multiplier);
            $oldQty = $batch->qty_masuk_base;
            $oldSisa = $batch->qty_sisa_base;

            // FIFO Rule check: Only if quantity is changing
            if ($oldQty !== $qty_masuk_base && $oldSisa < $oldQty) {
                throw new \Exception('Kuantitas batch tidak dapat diubah karena stok sudah mulai digunakan (FIFO Rule).');
            }

            $oldData = [
                'harga_beli' => (float)$batch->harga_beli_per_unit,
                'harga_jual' => (float)$batch->harga_jual_per_unit,
                'tanggal_masuk' => $batch->tanggal_masuk->format('Y-m-d'),
            ];

            $updateData = [
                'product_id' => $product->id,
                'input_unit_name' => $unitName,
                'qty_masuk_original' => $qty_input,
                'harga_beli_per_unit' => $data['harga_beli_per_base'],
                'harga_jual_per_unit' => $data['harga_jual_per_base'],
                'tanggal_masuk' => $data['tanggal_masuk'],
            ];

            // Only update qty fields if they actually changed (to avoid resetting qty_sisa)
            if ($oldQty !== $qty_masuk_base) {
                $updateData['qty_masuk_base'] = $qty_masuk_base;
                $updateData['qty_sisa_base'] = $qty_masuk_base;
            }

            $batch->update($updateData);

            $newData = [
                'harga_beli' => (float)$data['harga_beli_per_base'],
                'harga_jual' => (float)$data['harga_jual_per_base'],
                'tanggal_masuk' => $data['tanggal_masuk'],
            ];

            $changes = [];
            foreach ($oldData as $key => $value) {
                $isDifferent = false;
                if ($key === 'tanggal_masuk') {
                    $isDifferent = $value !== $newData[$key];
                } else {
                    // Use round for price comparisons to avoid float issues
                    $isDifferent = round($value, 2) !== round($newData[$key], 2);
                }

                if ($isDifferent) {
                    $changes['old'][$key] = $value;
                    $changes['new'][$key] = $newData[$key];
                }
            }

            // Log Update if qty changed or just general update
            if ($oldQty !== $qty_masuk_base || !empty($changes)) {
                \App\Models\ProductBatchLog::create([
                    'product_batch_id' => $batch->id,
                    'user_id' => auth()->id(),
                    'action' => 'updated',
                    'qty_change' => $qty_masuk_base - $oldQty,
                    'qty_before' => $oldQty,
                    'qty_after' => $qty_masuk_base,
                    'description' => $oldQty !== $qty_masuk_base ? 'Batch quantity updated via Edit form' : 'Batch details updated',
                    'changes' => !empty($changes) ? $changes : null,
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
