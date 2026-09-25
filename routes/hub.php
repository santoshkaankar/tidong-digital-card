<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Member\RestaurantController;

/*
|--------------------------------------------------------------------------
| Hub Services Routes (Food, Grocery, Taxi, etc.)
|--------------------------------------------------------------------------
*/

Route::middleware(['web', 'auth'])->prefix('hub')->name('hub.')->group(function () {

    // 1. Food & Restaurant Hub Routes
Route::prefix('restaurant')->name('restaurant.')->group(function () {
    Route::get('/', [RestaurantController::class, 'index'])->name('index');
    Route::get('/{id}', [RestaurantController::class, 'show'])->name('show');
    
    // Tiffin Booking Route (Corrected)
    Route::post('/{id}/book-tiffin', [RestaurantController::class, 'bookTiffin'])->name('bookTiffin');

    // Cart & Order
    Route::post('/cart/add', [RestaurantController::class, 'addToCart'])->name('cart.add');
    Route::get('/checkout/{restaurant_id}', [RestaurantController::class, 'checkout'])->name('checkout');
    Route::post('/order/place', [RestaurantController::class, 'placeOrder'])->name('order.place');
});

    // Future Hubs (Aage chal kar yahan easily add honge):
    // Route::prefix('grocery')->name('grocery.')->group(...);
    // Route::prefix('taxi')->name('taxi.')->group(...);
});