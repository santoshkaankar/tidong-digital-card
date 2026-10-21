<?php

use App\Http\Controllers\Vendor\VendorDeliveryController;
use Illuminate\Support\Facades\Route;

// Authenticated users still require firm-scoped permissions inside the service.
// Platform staff accounts are explicitly denied, regardless of other roles.
Route::middleware(['auth'])->prefix('delivery')->name('vendor.delivery.')->group(function (): void {
    Route::get('/', [VendorDeliveryController::class, 'index'])->name('index');
    Route::post('/partners/invite', [VendorDeliveryController::class, 'invite'])->name('partners.invite');
    Route::post('/businesses/{business}/partners/{partner}/verify', [VendorDeliveryController::class, 'verify'])
        ->whereNumber('business')->whereNumber('partner')->name('partners.verify');
    Route::post('/businesses/{business}/partners/{partner}/status', [VendorDeliveryController::class, 'status'])
        ->whereNumber('business')->whereNumber('partner')->name('partners.status');
    Route::post('/permissions', [VendorDeliveryController::class, 'grant'])->name('permissions.update');
    Route::post('/jobs', [VendorDeliveryController::class, 'store'])->name('jobs.store');
    Route::post('/jobs/{job}/reissue-pin', [VendorDeliveryController::class, 'reissuePin'])
        ->middleware('throttle:5,1')->whereNumber('job')->name('jobs.reissue-pin');
    Route::post('/jobs/{job}/cancel', [VendorDeliveryController::class, 'cancel'])->whereNumber('job')->name('jobs.cancel');
    Route::post('/jobs/{job}/return', [VendorDeliveryController::class, 'confirmReturn'])->whereNumber('job')->name('jobs.return');
    Route::post('/jobs/{job}/reassign', [VendorDeliveryController::class, 'reassign'])->whereNumber('job')->name('jobs.reassign');
});
