<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductBatchController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return redirect()->route('kasir.dashboard');
});

// Admin & Owner shared routes - Protected by admin session
Route::middleware(['admin.session'])->group(function () {
    // Dashboard - accessible by all with admin session
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/laporan', App\Livewire\Admin\ReportPage::class)->name('admin.reports.index');
    Route::get('/sales', App\Livewire\Admin\SalesPage::class)->name('admin.sales.index');
    Route::get('/sales/{invoice}', App\Livewire\Admin\SalesPage::class)->name('admin.sales.detail');
    Route::get('/debts', App\Livewire\Admin\AdminDebtList::class)->name('admin.debts.index');
    Route::get('/manajemen-user', App\Livewire\Admin\UserManagement::class)->name('admin.users.index');

    Route::get('/product-categories', [ProductCategoryController::class, 'index'])->name('product-categories.index');
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{id}/barcode', [ProductController::class, 'printBarcode'])->name('products.barcode');
    Route::get('/stok-masuk', [ProductBatchController::class, 'index'])->name('stok-masuk.index');
    Route::get('/stock-adjustments', App\Livewire\StockAdjustment\StockAdjustmentList::class)->name('admin.stock-adjustments.index');

    // Back to Kasir - Clears admin session
    Route::get('/back-to-kasir', function () {
        session()->forget('admin_authenticated');
        Auth::logout();
        return redirect('/');
    })->name('admin.back-to-kasir');
});

// Kasir Routes - Public access with auto-login
Route::middleware(['kasir.auto_login'])->group(function () {
    Route::prefix('kasir')->name('kasir.')->group(function () {
        Route::get('/dashboard', App\Livewire\Kasir\KasirDashboard::class)->name('dashboard');
        Route::get('/hutang', App\Livewire\Kasir\DebtPage::class)->name('debt');
    });
    Route::get('/transaksi', App\Livewire\Kasir\PosPage::class)->name('kasir.pos');
});
