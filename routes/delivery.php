<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Delivery\DeliveryController;

// Universal Delivery Partner Routes
Route::middleware(['auth'])->prefix('delivery')->name('delivery.')->group(function () {
    Route::get('/dashboard', [DeliveryController::class, 'index'])->name('dashboard');
    Route::post('/order/{id}/accept', [DeliveryController::class, 'acceptOrder'])->name('order.accept');
    Route::post('/order/{id}/deliver', [DeliveryController::class, 'markDelivered'])->name('order.deliver');
});