<?php

use App\Http\Controllers\Customer\SublimationOrderController;
use App\Http\Middleware\EnsureCustomerAccount;
use Illuminate\Support\Facades\Route;

// Anyone may browse active, approved, order-enabled personalized gifts.
Route::get('/sublimation', [SublimationOrderController::class, 'shop'])
    ->name('sublimation.shop');

Route::get('/sublimation/products/{product}', [SublimationOrderController::class, 'product'])
    ->whereNumber('product')->name('sublimation.products.show');

// Only authenticated Customers can submit and track their own requests.
Route::middleware(['auth', EnsureCustomerAccount::class])->group(function (): void {
    Route::post('/sublimation/orders', [SublimationOrderController::class, 'place'])
        ->middleware('throttle:6,1')->name('sublimation.orders.place');
    Route::get('/customer/sublimation-orders/{order}', [SublimationOrderController::class, 'order'])
        ->whereNumber('order')->name('customer.sublimation.orders.show');
    Route::get('/customer/sublimation-orders', [SublimationOrderController::class, 'orders'])
        ->name('customer.sublimation.orders');
});
