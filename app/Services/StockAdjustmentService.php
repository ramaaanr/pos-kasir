<?php

namespace App\Services;

use App\Models\StockAdjustment;
use App\Models\StockAdjustmentItem;
use App\Models\ProductBatch;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StockAdjustmentService
{
    /**
     * Create a new stock adjustment.
     *
     * @param array $data Header data (reason, date)
     * @param array $stagedItems Array of products with their batch adjustments
     * @return StockAdjustment
     * @throws \Exception
     */
    public function createAdjustment(array $data, array $stagedItems): StockAdjustment
    {
        return DB::transaction(function () use ($data, $stagedItems) {
            // 1. Create Header
            $adjustment = StockAdjustment::create([
                'user_id' => Auth::id(),
                'reason' => $data['reason'],
                'created_at' => now(), // User requested specific date? Usually system time for audit, but let's stick to now() for creation.
            ]);

            $totalChanges = 0;

            // 2. Process Items
            foreach ($stagedItems as $productGroup) {
                foreach ($productGroup['batches'] as $batchData) {
                    $qtyCurrent = (int)$batchData['qty_current'];
                    $qtyNew = (int)$batchData['qty_new'];

                    // ONLY process if quantity changed
                    if ($qtyCurrent !== $qtyNew) {
                        $batchId = $batchData['id'];
                        $batch = ProductBatch::findOrFail($batchId);

                        // Validation: Cannot adjust to negative unless logical business rule permits (usually stock cannot be negative)
                        if ($qtyNew < 0) {
                            throw new \Exception("Stok tidak boleh kurang dari 0 untuk batch {$batch->batch_code}");
                        }

                        // Create Item Record
                        StockAdjustmentItem::create([
                            'stock_adjustment_id' => $adjustment->id,
                            'product_batch_id' => $batchId,
                            'qty_before_base' => $qtyCurrent,
                            'qty_after_base' => $qtyNew,
                        ]);

                        // Update Batch Stock
                        $diff = $qtyNew - $qtyCurrent;
                        $batch->qty_masuk_base += $diff;
                        $batch->qty_sisa_base = $qtyNew;
                        $batch->save();

                        // Log Batch Change
                        \App\Models\ProductBatchLog::create([
                            'product_batch_id' => $batchId,
                            'user_id' => Auth::id(),
                            'action' => 'adjustment',
                            'qty_change' => $qtyNew - $qtyCurrent,
                            'qty_before' => $qtyCurrent,
                            'qty_after' => $qtyNew,
                            'description' => 'Stock Adjustment: ' . $data['reason'],
                        ]);

                        $totalChanges++;
                    }
                }
            }

            if ($totalChanges === 0) {
                throw new \Exception("Tidak ada perubahan stok yang terdeteksi. Pastikan Anda mengubah jumlah stok setidaknya pada satu batch.");
            }

            return $adjustment;
        });
    }

    /**
     * Get paginated adjustments for list view.
     */
    public function getPaginatedAdjustments(int $perPage = 10)
    {
        return StockAdjustment::with(['user', 'items'])
            ->latest()
            ->paginate($perPage);
    }
}
