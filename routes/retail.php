<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Retail\ShopController;
use App\Http\Controllers\Retail\CartController;
use App\Http\Controllers\Retail\CheckoutController;

Route::middleware(['auth'])->prefix('retail')->name('retail.')->group(function () {
    // Retail Dashboard Route with Strict Guard
    Route::get('/dashboard', function () {
        if (auth()->user()->business_type !== 'retail' && auth()->user()->role !== 'admin') {
            return redirect()->route('dashboard');
        }
        return view('retail.dashboard');
    })->name('dashboard');
});

Route::prefix('shop')->name('retail.shop.')->group(function () {
    Route::get('/', [ShopController::class, 'index'])->name('index');
    Route::get('/product/{id}', [ShopController::class, 'show'])->name('show');
});

Route::prefix('cart')->name('retail.cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'add'])->name('add');
});

Route::prefix('checkout')->name('retail.checkout.')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('index');
    Route::post('/process', [CheckoutController::class, 'process'])->name('process');
});