<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Payment\WalletController;
use App\Http\Controllers\Payment\GlobalPaymentController;

Route::middleware(['web'])->prefix('payment')->name('payment.')->group(function () {
    Route::get('/checkout/{orderId}', [GlobalPaymentController::class, 'checkout'])->name('checkout');
    Route::post('/process', [GlobalPaymentController::class, 'processPayment'])->name('process');
    Route::post('/callback', [GlobalPaymentController::class, 'paymentCallback'])->name('callback');
});

Route::middleware(['web', 'auth'])->prefix('vendor')->name('vendor.')->group(function () {
    Route::get('/wallet', [WalletController::class, 'index'])->name('wallet.index');
    Route::post('/wallet/withdraw', [WalletController::class, 'withdraw'])->name('wallet.withdraw');
});