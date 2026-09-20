<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Shopping\ItemController;
use App\Http\Controllers\Shopping\CartController;
use App\Http\Controllers\Shopping\CheckoutController;
use App\Http\Controllers\Shopping\CategoryController;
use App\Http\Controllers\Shopping\BrandController;
use App\Http\Controllers\Shopping\VariantController;
use App\Http\Controllers\Shopping\UnitOfMeasureController;

Route::prefix('shopping')->name('shopping.')->group(function () {
    
    // Public / Customer Shopping Routes
    Route::get('/', [ItemController::class, 'index'])->name('index');
    Route::get('/shop', [ItemController::class, 'shop'])->name('shop');
    Route::get('/product/{id}', [ItemController::class, 'show'])->name('product.detail');
    Route::get('/category/{id}', [CategoryController::class, 'show'])->name('category');
    Route::get('/vendor/{id}', [ItemController::class, 'vendorStore'])->name('vendor.store');
    
    // Cart & Wishlist Routes
    Route::get('/cart', [CartController::class, 'index'])->name('cart');
    Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/coupon', [CartController::class, 'applyCoupon'])->name('cart.coupon');
    
    Route::get('/wishlist', [CartController::class, 'wishlist'])->name('wishlist');
    Route::post('/wishlist/toggle/{id}', [CartController::class, 'toggleWishlist'])->name('wishlist.toggle');

    // Authenticated & Shopping Vendor Dashboard Routes
    Route::middleware(['auth'])->group(function () {
        Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
        Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
        Route::get('/order-success/{id}', [CheckoutController::class, 'success'])->name('order.success');
        Route::get('/my-orders', [CheckoutController::class, 'orders'])->name('orders');

        // Updated Shop Dashboard Route (Replaced Admin Dashboard)
        Route::get('/shop-dashboard', function () {
            return view('shopping.shop-dashboard');
        })->name('shop-dashboard');

        // CRUD Resource Routes for E-Commerce Management
        Route::resource('items', ItemController::class);
        Route::resource('categories', CategoryController::class);
        Route::resource('brands', BrandController::class);
        Route::resource('variants', VariantController::class);
        Route::resource('units', UnitOfMeasureController::class);
    });

});