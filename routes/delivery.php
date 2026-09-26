<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Delivery\DeliveryDashboardController;
use App\Http\Controllers\Delivery\DeliveryOrderController;
use App\Http\Controllers\Delivery\DeliveryEarningsController;
use App\Http\Controllers\Delivery\DeliveryProfileController;

Route::middleware(['auth'])->prefix('delivery')->name('delivery.')->group(function () {
    
    // 1. Dashboard
    Route::get('/dashboard', [DeliveryDashboardController::class, 'index'])->name('dashboard');

    // 2. Orders Workflow
    Route::get('/orders', [DeliveryOrderController::class, 'index'])->name('orders.index');
    Route::post('/orders/{id}/accept', [DeliveryOrderController::class, 'accept'])->name('orders.accept');
    Route::post('/orders/{id}/complete', [DeliveryOrderController::class, 'complete'])->name('orders.complete');
    Route::post('/orders/{id}/update-status', [DeliveryOrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::get('/orders/history', [DeliveryOrderController::class, 'history'])->name('orders.history');

    // 3. Earnings & Payouts
    Route::get('/earnings', [DeliveryEarningsController::class, 'index'])->name('earnings.index');

    // 4. Profile & Settings
    Route::get('/profile', [DeliveryProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile', [DeliveryProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/toggle-duty', [DeliveryProfileController::class, 'toggleDuty'])->name('profile.toggle-duty');
    Route::get('/pincode-lookup/{pincode}', [DeliveryProfileController::class, 'lookupPincode'])->name('pincode.lookup');

    // Live Pincode & Area Auto-Search Routes
    Route::get('/pincode-lookup/{pincode}', [DeliveryProfileController::class, 'lookupPincode'])->name('pincode.lookup');
    Route::get('/area-search', [DeliveryProfileController::class, 'searchArea'])->name('area.search');

});