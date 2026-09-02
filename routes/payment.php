<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Payment\GlobalPaymentController;

Route::middleware(['web'])->prefix('payment')->name('payment.')->group(function () {
    Route::get('/checkout/{orderId}', [GlobalPaymentController::class, 'checkout'])->name('checkout');
    Route::post('/process', [GlobalPaymentController::class, 'processPayment'])->name('process');
    Route::post('/callback', [GlobalPaymentController::class, 'paymentCallback'])->name('callback');
});