<?php

use App\Http\Controllers\Vendor\SublimationStudioController;
use App\Http\Controllers\Vendor\SublimationOperationsController;
use App\Http\Middleware\EnsureVendorAccount;
use Illuminate\Support\Facades\Route;

// Included from routes/vendor.php, which is already mounted under /vendor.
// Platform employees are blocked, and the controller enforces Vendor + Business + Branch ownership.
Route::middleware(['auth', EnsureVendorAccount::class])->prefix('businesses/{business}/sublimation')
    ->where(['business' => '[0-9]+'])->group(function (): void {
        Route::get('/', [SublimationStudioController::class, 'index'])->name('vendor.sublimation.index');
        Route::get('/products', [SublimationStudioController::class, 'products'])->name('vendor.sublimation.products.index');
        Route::get('/products/create', [SublimationStudioController::class, 'createProduct'])->name('vendor.sublimation.products.create');
        Route::get('/products/{product}', [SublimationStudioController::class, 'product'])
            ->whereNumber('product')->name('vendor.sublimation.products.show');
        Route::get('/orders', [SublimationStudioController::class, 'orders'])->name('vendor.sublimation.orders.index');
        Route::get('/orders/create', [SublimationStudioController::class, 'createOrder'])->name('vendor.sublimation.orders.create');
        Route::get('/orders/{order}', [SublimationStudioController::class, 'order'])
            ->whereNumber('order')->name('vendor.sublimation.orders.show');
        Route::get('/materials', [SublimationOperationsController::class, 'materials'])->name('vendor.sublimation.materials.index');
        Route::post('/materials', [SublimationOperationsController::class, 'createMaterial'])->name('vendor.sublimation.materials.store');
        Route::post('/materials/{material}/movement', [SublimationOperationsController::class, 'moveMaterial'])
            ->whereNumber('material')->name('vendor.sublimation.materials.movement');
        Route::get('/reports', [SublimationOperationsController::class, 'reports'])->name('vendor.sublimation.reports.index');
        Route::post('/products', [SublimationStudioController::class, 'storeProduct'])->name('vendor.sublimation.products.store');
        Route::patch('/products/{product}', [SublimationStudioController::class, 'updateProduct'])
            ->whereNumber('product')->name('vendor.sublimation.products.update');
        Route::post('/orders', [SublimationStudioController::class, 'storeOrder'])->name('vendor.sublimation.orders.store');
        Route::post('/orders/{order}/status', [SublimationStudioController::class, 'updateStatus'])
            ->whereNumber('order')->name('vendor.sublimation.orders.status');
    });
