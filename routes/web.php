<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductBatchController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->to('/login');
})->name('filament.admin.pages.dashboard');

Route::middleware(['auth'])->group(function () {
    // Dashboard - accessible by all authenticated users (renders role-specific view)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Admin-only routes
    Route::middleware(['role:admin,owner'])->group(function () {
        Route::get('/product-categories', [ProductCategoryController::class, 'index'])->name('product-categories.index');
        Route::get('/products', [ProductController::class, 'index'])->name('products.index');
        Route::get('/products/{id}/barcode', [ProductController::class, 'printBarcode'])->name('products.barcode');
        Route::get('/stok-masuk', [ProductBatchController::class, 'index'])->name('stok-masuk.index');
        Route::get('/stock-adjustments', App\Livewire\StockAdjustment\StockAdjustmentList::class)->name('admin.stock-adjustments.index');
    });
    
    // Kasir Routes - accessible by kasir role & owner
    Route::middleware(['role:kasir,owner'])->group(function () {
        Route::prefix('kasir')->name('kasir.')->group(function () {
            Route::get('/dashboard', App\Livewire\Kasir\KasirDashboard::class)->name('dashboard');
            Route::get('/hutang', App\Livewire\Kasir\DebtPage::class)->name('debt');
        });
        Route::get('/transaksi', App\Livewire\Kasir\PosPage::class)->name('kasir.pos');
    });
});
