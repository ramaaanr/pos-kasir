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
    return redirect()->to('/admin/login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/product-categories', [ProductCategoryController::class, 'index'])->name('product-categories.index');
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{id}/barcode', [ProductController::class, 'printBarcode'])->name('products.barcode');
    Route::get('/stok-masuk', [ProductBatchController::class, 'index'])->name('stok-masuk.index');
    Route::get('/stock-adjustments', App\Livewire\StockAdjustment\StockAdjustmentList::class)->name('admin.stock-adjustments.index');
});
