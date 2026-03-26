<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Models\Sale;
use App\Models\ShiftReport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

// Mock login
$user = App\Models\User::first();
if (!$user) {
    echo "No user found\n";
    exit;
}
Auth::login($user);

DB::enableQueryLog();
$start = microtime(true);

// Replicate KasirDashboard render logic
$today = now()->startOfDay();
$todayTransactionCount = Sale::where('created_at', '>=', $today)
    ->where('status', 'completed')
    ->count();

$todayTotalSales = Sale::where('created_at', '>=', $today)
    ->where('status', 'completed')
    ->sum('total');

$recentTransactions = Sale::with('customer')
    ->latest()
    ->paginate(10, pageName: 'transactions');

$stockData = Product::active()
    ->withSum('batches', 'qty_sisa_base')
    ->orderBy('batches_sum_qty_sisa_base', 'asc')
    ->paginate(10, pageName: 'stock');

// Calculate system cash (simplified)
$activeShift = ShiftReport::where('user_id', Auth::id())->where('status', 'open')->first();
if ($activeShift) {
    $cashSales = Sale::where('user_id', Auth::id())
        ->where('status', '!=', 'cancelled')
        ->where('payment_method', 'cash')
        ->where('created_at', '>=', $activeShift->start_time)
        ->sum('total');
}

$end = microtime(true);
$queries = DB::getQueryLog();

echo "Execution time: " . ($end - $start) . " seconds\n";
echo "Total queries: " . count($queries) . "\n";
foreach ($queries as $q) {
    echo "Query ({$q['time']}ms): {$q['query']}\n";
}
