<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Vendor\Money_Exchange\ExchangeRateController;

Route::middleware(['auth', 'role:vendor'])
    ->prefix('vendor/money-exchange')
    ->name('vendor.exchange.')
    ->group(function () {
        Route::get('/dashboard', [ExchangeRateController::class, 'dashboard'])->name('dashboard');
        Route::get('/rates', [ExchangeRateController::class, 'dashboard'])->name('rates');
        Route::post('/store', [ExchangeRateController::class, 'store'])->name('store');
    });