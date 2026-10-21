<?php

use App\Http\Controllers\Vendor\SublimationStudioController;
use App\Http\Middleware\EnsureVendorAccount;
use Illuminate\Support\Facades\Route;

// Included from routes/vendor.php, which is already mounted under /vendor.
// Platform employees are blocked, and the controller enforces Vendor + Business + Branch ownership.
Route::middleware(['auth', EnsureVendorAccount::class])->prefix('businesses/{business}/sublimation')
    ->where(['business' => '[0-9]+'])->group(function (): void {
        Route::get('/', [SublimationStudioController::class, 'index'])->name('vendor.sublimation.index');
        Route::post('/products', [SublimationStudioController::class, 'storeProduct'])->name('vendor.sublimation.products.store');
        Route::patch('/products/{product}', [SublimationStudioController::class, 'updateProduct'])
            ->whereNumber('product')->name('vendor.sublimation.products.update');
        Route::post('/orders', [SublimationStudioController::class, 'storeOrder'])->name('vendor.sublimation.orders.store');
        Route::post('/orders/{order}/status', [SublimationStudioController::class, 'updateStatus'])
            ->whereNumber('order')->name('vendor.sublimation.orders.status');
    });
