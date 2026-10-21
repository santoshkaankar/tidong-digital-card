<?php

use App\Http\Controllers\Grocery\GroceryShopController as Shop;
use App\Http\Middleware\EnsureCustomerAccount;
use Illuminate\Support\Facades\Route;

// Mounted from web.php (no separate Laravel app). Public: browse only; no registration opened.
Route::prefix('grocery')->name('grocery.shop.')->middleware('throttle:90,1')->group(function (): void {
    Route::get('/', [Shop::class, 'stores'])->name('stores');
    Route::post('/cart/clear', [Shop::class, 'clear'])->name('cart.clear')->middleware('throttle:30,1');
    Route::middleware(['auth', EnsureCustomerAccount::class])->group(function (): void {
        Route::get('/my-orders', [Shop::class, 'myOrders'])->name('orders.index');
        Route::get('/my-orders/{order}', [Shop::class, 'order'])->whereNumber('order')->name('orders.show');
        Route::post('/my-orders/{order}/cancel', [Shop::class, 'cancel'])->whereNumber('order')->name('orders.cancel');
    });
    Route::prefix('{business}/{branch}')->where(['business' => '[0-9]+', 'branch' => '[0-9]+'])->group(function (): void {
        Route::get('/', [Shop::class, 'catalog'])->name('catalog');
        Route::get('/products/{product}', [Shop::class, 'detail'])->whereNumber('product')->name('products.show');
        Route::get('/basket', [Shop::class, 'basket'])->name('basket');
        Route::post('/basket/items', [Shop::class, 'add'])->name('cart.add')->middleware('throttle:30,1');
        Route::patch('/basket/items', [Shop::class, 'updateBasket'])->name('cart.update')->middleware('throttle:30,1');
        Route::middleware(['auth', EnsureCustomerAccount::class])->group(function (): void {
            Route::get('/checkout', [Shop::class, 'checkout'])->name('checkout');
            Route::post('/checkout', [Shop::class, 'place'])->name('checkout.place')->middleware('throttle:10,1');
        });
    });
});
