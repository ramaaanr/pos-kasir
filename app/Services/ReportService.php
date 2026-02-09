<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\Debt;
use App\Models\DebtPayment;
use App\Models\Customer;
use App\Models\ShiftReport;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * 1. Laporan Shift Harian (Admin View of Cashier Shift)
     */
    public function getDailyShiftReport($userId = null, $startDate = null, $endDate = null)
    {
        $query = Sale::query()
            ->whereIn('status', ['completed', 'partial'])
            ->when($userId, fn($q) => $q->where('user_id', $userId))
            ->when($startDate, fn($q) => $q->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('created_at', '<=', $endDate));

        return [
            'total_sales' => (clone $query)->sum('total'),
            'total_cash' => (clone $query)->where('payment_method', 'cash')->sum('total'),
            'total_debt' => (clone $query)->where('payment_method', 'debt')->sum('total'),
            'total_dp' => (clone $query)->where('payment_method', 'debt')->sum('total_paid'),
            // Total masuk ke kasir sistem (Cash + DP dari Hutang)
            'total_masuk_kas' => (clone $query)->where('payment_method', 'cash')->sum('total') + (clone $query)->where('payment_method', 'debt')->sum('total_paid'),
            'transaction_count' => $query->count(),
            'method_summary' => $query->select('payment_method', DB::raw('count(*) as count'), DB::raw('sum(total) as total'))
                ->groupBy('payment_method')
                ->get(),
            'transactions' => $query->with('user')->latest()->take(100)->get()
        ];
    }

    /**
     * 2. Laporan Stok Real-time
     */
    public function getRealtimeStockReport()
    {
        return Product::where('is_active', true)
            ->withSum('batches as total_stock', 'qty_sisa_base')
            ->orderBy('nama')
            ->get()
            ->map(function($product) {
                $status = 'aman';
                if ($product->total_stock <= 0) $status = 'habis';
                elseif ($product->total_stock < 10) $status = 'menipis'; // Threshold logic
                
                $product->stock_status = $status;
                return $product;
            });
    }

    /**
     * 3. Laporan Penjualan Per Periode
     */
    public function getPeriodicSalesReport($startDate, $endDate)
    {
        return Sale::whereIn('status', ['completed', 'partial'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->with(['user', 'items.product'])
            ->latest()
            ->get();
    }

    /**
     * 4. Laporan Hutang Outstanding
     */
    public function getOutstandingDebtReport()
    {
        return Debt::whereIn('status', ['OPEN', 'PARTIAL'])
            ->with(['customer', 'sale'])
            ->get()
            ->map(function($debt) {
                // Round up: any partial day counts as 1 full day
                $debt->days_old = (int) ceil(now()->diffInSeconds($debt->created_at) / 86400);
                return $debt;
            });
    }

    /**
     * 5. Laporan Pembayaran Hutang
     */
    public function getDebtPaymentReport($startDate, $endDate)
    {
        return DebtPayment::whereDate('paid_at', '>=', $startDate)
            ->whereDate('paid_at', '<=', $endDate)
            ->with(['customer', 'user', 'debt.sale'])
            ->latest()
            ->get();
    }

    /**
     * 6. Laporan Laba Rugi / Margin (FIFO)
     */
    public function getProfitMarginReport($startDate, $endDate)
    {
        // Join sales -> sale_items -> sale_item_batches
        return DB::table('sales')
            ->join('sale_items', 'sales.id', '=', 'sale_items.sale_id')
            ->join('sale_item_batches', 'sale_items.id', '=', 'sale_item_batches.sale_item_id')
            ->whereIn('sales.status', ['completed', 'partial'])
            ->whereDate('sales.created_at', '>=', $startDate)
            ->whereDate('sales.created_at', '<=', $endDate)
            ->select(
                'sales.id as sale_id',
                'sales.invoice_number',
                'sales.created_at',
                DB::raw('SUM(sale_item_batches.qty_base * sale_items.harga_jual_per_unit / sale_items.unit_multiplier) as total_revenue'), 
                // We use sale_items.harga_jual_per_unit / unit_multiplier to get price per base unit
                DB::raw('SUM(sale_item_batches.qty_base * sale_item_batches.harga_beli_per_unit) as total_cogs')
            )
            ->groupBy('sales.id', 'sales.invoice_number', 'sales.created_at')
            ->get()
            ->map(function($row) {
                // Since sales.total is the actual final price paid (after discounts potentially)
                // we should stick to actual revenue if possible, but for per-item breakdown 
                // we use the calculated values.
                $row->gross_profit = $row->total_revenue - $row->total_cogs;
                $row->margin_percent = $row->total_revenue > 0 ? ($row->gross_profit / $row->total_revenue) * 100 : 0;
                return $row;
            });
    }

    /**
     * 7. Laporan FIFO Compliance
     */
    public function getFIFOComplianceReport()
    {
        // Logic to find if any sale used a batch that was newer than an available older batch
        // This is complex for a single query, providing a utility for now.
        return DB::table('sale_item_batches')
            ->join('product_batches', 'sale_item_batches.product_batch_id', '=', 'product_batches.id')
            ->join('sale_items', 'sale_item_batches.sale_item_id', '=', 'sale_items.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->select(
                'sales.invoice_number',
                'product_batches.batch_code',
                'product_batches.tanggal_masuk as batch_date',
                'sale_item_batches.qty_base',
                'sale_item_batches.created_at as sale_date'
            )
            ->orderBy('sale_item_batches.created_at', 'desc')
            ->limit(100)
            ->get();
    }

    /**
     * 8. Inventory Valuation Report (Real-time)
     */
    public function getInventoryValuationReport()
    {
        return Product::where('is_active', true)
            ->with(['batches' => fn($q) => $q->where('qty_sisa_base', '>', 0)])
            ->get()
            ->map(function($product) {
                $totalStock = $product->batches->sum('qty_sisa_base');
                $totalValuation = $product->batches->sum(fn($b) => $b->qty_sisa_base * $b->harga_beli_per_unit);
                $avgBuyingPrice = $totalStock > 0 ? $totalValuation / $totalStock : 0;

                return [
                    'id' => $product->id,
                    'nama' => $product->nama,
                    'total_stock' => $totalStock,
                    'avg_buying_price' => $avgBuyingPrice,
                    'total_valuation' => $totalValuation,
                ];
            });
    }

    /**
     * 9. Debt Aging Report (Based on created_at)
     */
    public function getDebtAgingReport()
    {
        return Customer::whereHas('debts', fn($q) => $q->whereIn('status', ['OPEN', 'PARTIAL']))
            ->with(['debts' => fn($q) => $q->whereIn('status', ['OPEN', 'PARTIAL'])->with('payments')])
            ->get()
            ->map(function($customer) {
                $aging = [
                    'current' => 0, // 0-30 days
                    'at_risk' => 0, // 31-60 days
                    'danger' => 0,  // > 60 days
                    'total' => 0
                ];

                foreach ($customer->debts as $debt) {
                    $days = now()->diffInDays($debt->created_at);
                    $remaining = $debt->amount - $debt->payments->sum('amount');
                    
                    if ($days <= 30) $aging['current'] += $remaining;
                    elseif ($days <= 60) $aging['at_risk'] += $remaining;
                    else $aging['danger'] += $remaining;
                    
                    $aging['total'] += $remaining;
                }

                return [
                    'customer_name' => $customer->nama,
                    'aging' => $aging
                ];
            });
    }

    /**
     * 10. Cashier Performance Report
     */
    public function getCashierPerformanceReport($startDate, $endDate)
    {
        return DB::table('users')
            ->join('sales', 'users.id', '=', 'sales.user_id')
            ->whereIn('sales.status', ['completed', 'partial'])
            ->whereDate('sales.created_at', '>=', $startDate)
            ->whereDate('sales.created_at', '<=', $endDate)
            ->select(
                'users.id',
                'users.name',
                DB::raw('COUNT(sales.id) as transaction_count'),
                DB::raw('SUM(sales.total) as total_sales'),
                DB::raw('AVG(sales.total) as avg_transaction_value')
            )
            ->groupBy('users.id', 'users.name')
            ->get()
            ->map(function($row) use ($startDate, $endDate) {
                // Attach total cash difference from shift reports
                $row->total_difference = DB::table('shift_reports')
                    ->where('user_id', $row->id)
                    ->whereDate('start_time', '>=', $startDate)
                    ->whereDate('start_time', '<=', $endDate)
                    ->sum('difference');
                return $row;
            });
    }

    /**
     * 11. Slow Moving Stock Report
     */
    public function getSlowMovingReport()
    {
        return Product::where('is_active', true)
            ->withSum('batches as total_stock', 'qty_sisa_base')
            ->having('total_stock', '>', 0)
            ->get()
            ->map(function($product) {
                $lastSold = $product->last_sold_at;
                $daysSinceLastSale = $lastSold ? now()->diffInDays($lastSold) : now()->diffInDays($product->created_at);
                
                $status = 'healthy';
                if ($daysSinceLastSale > 90) $status = 'dead';
                elseif ($daysSinceLastSale > 60) $status = 'slow';
                elseif ($daysSinceLastSale > 30) $status = 'warning';

                return [
                    'nama' => $product->nama,
                    'total_stock' => $product->total_stock,
                    'last_sold_at' => $lastSold,
                    'days_inactive' => $daysSinceLastSale,
                    'status' => $status
                ];
            })
            ->sortByDesc('days_inactive')
            ->values();
    }

    /**
     * 12. Monthly Business Stats for Dashboard
     */
    public function getMonthlyStats()
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        $data = DB::table('sale_item_batches')
            ->join('sale_items', 'sale_item_batches.sale_item_id', '=', 'sale_items.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereIn('sales.status', ['completed', 'partial'])
            ->whereBetween('sales.created_at', [$startOfMonth, $endOfMonth])
            ->select(
                DB::raw('SUM(sale_item_batches.qty_base * sale_items.harga_jual_per_unit / sale_items.unit_multiplier) as revenue'),
                DB::raw('SUM(sale_item_batches.qty_base * sale_item_batches.harga_beli_per_unit) as cogs')
            )
            ->first();

        $revenue = $data->revenue ?? 0;
        $cogs = $data->cogs ?? 0;
        $profit = $revenue - $cogs;
        $margin = $revenue > 0 ? ($profit / $revenue) * 100 : 0;

        return [
            'revenue' => (float) $revenue,
            'cogs' => (float) $cogs,
            'profit' => (float) $profit,
            'margin' => (float) $margin,
        ];
    }

    /**
     * 13. Dashboard Recent Activity
     */
    public function getDashboardData()
    {
        return [
            'recent_sales' => Sale::with('customer', 'user')
                ->whereIn('status', ['completed', 'partial'])
                ->latest()
                ->limit(5)
                ->get(),
            'recent_debts' => Debt::with('customer')
                ->whereIn('status', ['OPEN', 'PARTIAL'])
                ->latest()
                ->limit(5)
                ->get()
        ];
    }
}

