<?php

use App\Http\Controllers\Admin\AboutController;
use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BroadcastController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FavoriteBrandController;
use App\Http\Controllers\Admin\LogbookController;
use App\Http\Controllers\Admin\ProductBrandController;
use App\Http\Controllers\Admin\ProductCategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductStatusController;
use App\Http\Controllers\Admin\SalesOrderController;
use Illuminate\Support\Facades\Route;

// Include guest routes
require __DIR__.'/guest.php';

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest:web')->group(function () {
        Route::get('login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('login', [AuthController::class, 'login'])->name('login.store');
    });

    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

    Route::middleware(['admin'])->group(function () {
        Route::redirect('/', '/admin/dashboard');
        Route::get('dashboard', DashboardController::class)->name('dashboard');

        Route::resource('accounts', AccountController::class)->except(['show']);
        Route::resource('customers', CustomerController::class)->except(['show']);
        Route::patch('customers/{customer}/approve', [CustomerController::class, 'approve'])->name('customers.approve');
        Route::patch('customers/{customer}/reject', [CustomerController::class, 'reject'])->name('customers.reject');
        Route::resource('categories', ProductCategoryController::class)->except(['show']);
        Route::resource('brands', ProductBrandController::class)->except(['show']);
        Route::resource('products', ProductController::class)->except(['show']);
        Route::resource('statuses', ProductStatusController::class)->except(['show']);

        Route::resource('sales-orders', SalesOrderController::class)->only(['index', 'show', 'edit', 'update', 'destroy']);
        Route::resource('broadcasts', BroadcastController::class)->except(['show']);
        Route::resource('favorite-brands', FavoriteBrandController::class)->only(['index', 'create', 'store', 'destroy']);
        Route::get('logs', [LogbookController::class, 'index'])->name('logs.index');
        Route::get('about', [AboutController::class, 'edit'])->name('about.edit');
        Route::post('about', [AboutController::class, 'update'])->name('about.update');
    });
});
