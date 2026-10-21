<?php

use App\Http\Controllers\Vendor\GroceryController;
use App\Http\Middleware\EnsureVendorAccount;
use Illuminate\Support\Facades\Route;

// Included from routes/vendor.php, already mounted under /vendor.
Route::middleware(['auth', EnsureVendorAccount::class])
    ->prefix('businesses/{business}/grocery')
    ->where(['business' => '[0-9]+'])
    ->name('vendor.grocery.')
    ->controller(GroceryController::class)
    ->group(function (): void {
        Route::get('/', 'index')->name('index');
        Route::post('/branch-settings', 'branchFeature')->name('branch-settings');
        Route::post('/products', 'product')->name('products.store');
        Route::patch('/products/{product}/pricing', 'price')->whereNumber('product')->name('products.price');
        Route::patch('/products/{product}/availability', 'availability')->whereNumber('product')->name('products.availability');
        Route::post('/stock-receipts', 'receive')->name('stock.receive');
        Route::post('/counter-sales', 'sale')->name('sales.store');
        Route::post('/counter-sales/{sale}/reversal', 'reverse')->whereNumber('sale')->name('sales.reverse');
    });


// Vendor-only online order desk, isolated from Customer storefront.
Route::middleware(['auth', EnsureVendorAccount::class])
    ->prefix('businesses/{business}/grocery/online-orders')
    ->where(['business' => '[0-9]+'])
    ->name('vendor.grocery.online.')
    ->controller(\App\Http\Controllers\Vendor\GroceryOnlineOrdersController::class)
    ->group(function (): void {
        Route::get('/', 'index')->name('index');
        Route::get('/{order}', 'show')->whereNumber('order')->name('show');
        Route::post('/{order}/status', 'update')->whereNumber('order')->name('status');
    });

// Edit only display metadata; pricing and inventory retain their dedicated audited routes.
Route::middleware(['auth', EnsureVendorAccount::class])
    ->patch('businesses/{business}/grocery/products/{product}/presentation',
        [\App\Http\Controllers\Vendor\GroceryProductPresentationController::class, 'update'])
    ->where(['business' => '[0-9]+', 'product' => '[0-9]+'])
    ->name('vendor.grocery.products.presentation');
