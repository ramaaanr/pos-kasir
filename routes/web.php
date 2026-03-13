<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductBatchController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    if (!Auth::check()) {
        $kasir = \App\Models\User::where('email', 'kasir@pos.com')->first();
        if ($kasir) {
            Auth::login($kasir);
        }
    }

    if (Auth::check() && Auth::user()->hasRole('admin')) {
        return redirect('/admin');
    }

    return redirect()->route('kasir.pos');
});

Route::middleware(['auth'])->group(function () {
    // Dashboard - accessible by all authenticated users (renders role-specific view)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Admin & Owner shared routes
    Route::middleware(['role:admin,owner'])->group(function () {
        Route::get('/laporan', App\Livewire\Admin\ReportPage::class)->name('admin.reports.index');
        Route::get('/sales', App\Livewire\Admin\SalesPage::class)->name('admin.sales.index');
        Route::get('/sales/{invoice}', App\Livewire\Admin\SalesPage::class)->name('admin.sales.detail');
        Route::get('/debts', App\Livewire\Admin\AdminDebtList::class)->name('admin.debts.index');
        Route::get('/manajemen-user', App\Livewire\Admin\UserManagement::class)->name('admin.users.index');
    });

    // Admin-only routes (Owner excluded)
    Route::middleware(['role:admin'])->group(function () {
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
