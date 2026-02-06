<?php

use App\Models\User;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\Debt;
use App\Services\SaleService;
use Illuminate\Support\Facades\Auth;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = User::first();
Auth::login($user);

$customer = Customer::firstOrCreate(['no_hp' => '08666666666'], ['nama' => 'Test POS Script']);
$startDebt = (int) $customer->total_debt;

$sale = Sale::create([
    'invoice_number' => 'INV-SCRIPT-' . rand(100, 999),
    'total' => 120000,
    'status' => 'draft',
    'payment_method' => 'cash', // Default for draft
    'user_id' => $user->id
]);

\App\Models\SaleItem::create([
    'sale_id' => $sale->id,
    'product_id' => 1,
    'product_batch_id' => 1,
    'qty_base' => 10,
    'unit_label' => 'unit',
    'unit_multiplier' => 1,
    'harga_jual_per_unit' => 12000,
    'subtotal' => 120000
]);

$service = app(SaleService::class);
$service->finalizeSale($sale, [
    'method' => 'debt',
    'customer_name' => 'Test POS Script',
    'customer_phone' => '08666666666',
    'jaminan' => 'STNK',
    'partial_amount' => 50000
]);

$debt = Debt::where('sale_id', $sale->id)->first();
$customer->refresh();

echo "Debt Status: " . $debt->status . "\n";
echo "Debt Amount: " . $debt->amount . "\n";
echo "Debt Payments Count: " . $debt->payments()->count() . "\n";
echo "Total Paid in Sale: " . $sale->fresh()->total_paid . "\n";
echo "Customer Debt Start: " . $startDebt . "\n";
echo "Customer Debt End: " . $customer->total_debt . "\n";
echo "Increase: " . ($customer->total_debt - $startDebt) . "\n";

if ($debt->status === 'PARTIAL' && ($customer->total_debt - $startDebt) === 70000) {
    echo "VERIFICATION SUCCESSFUL\n";
} else {
    echo "VERIFICATION FAILED\n";
}
