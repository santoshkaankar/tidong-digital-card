<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Vendor\VendorCatalogController;
use App\Http\Controllers\Vendor\VendoritemController;
use App\Http\Controllers\Vendor\CatalogController;
use App\Http\Controllers\Vendor\VendorDashboardController;
use App\Http\Controllers\Vendor\ExchangeRateController;

// Auth & Business Role Middleware ke andar sabhi Vendor Routes
Route::middleware(['auth', 'role:business'])->group(function () {
    
    // Business Dashboard Route
    Route::get('/dashboard', [VendorDashboardController::class, 'index'])->name('dashboard');

    // Duty Status Switch (Taxi & Guide Online/Offline)
    Route::post('/duty-status', [ExchangeRateController::class, 'updateDutyStatus'])->name('duty.status');

    // Service Specific Routes (Taxi, Money Exchange, Guide)
    Route::get('/taxi/rides', [ExchangeRateController::class, 'taxiRides'])->name('taxi.rides');
    Route::get('/exchange/rates', [ExchangeRateController::class, 'ratesIndex'])->name('exchange.rates');
    Route::get('/guide/bookings', [ExchangeRateController::class, 'guideBookings'])->name('guide.bookings');

    // Kitchen Dashboard Routes
    Route::get('/kitchen', [VendoritemController::class, 'kitchenDashboard'])->name('kitchen.orders');
    Route::get('/kitchen/live', [VendoritemController::class, 'kitchenDashboard'])->name('kitchen.dashboard');
    Route::post('/kitchen/order/complete/{orderId}', [VendoritemController::class, 'completeOrder'])->name('public.order.complete');

    // Profile Routes
    Route::get('/profile', [VendorDashboardController::class, 'profile'])->name('profile');
    Route::post('/profile/update', [VendorDashboardController::class, 'updateProfile'])->name('profile.update');

    // Pricing Routes
    Route::get('/pricing', [VendorCatalogController::class, 'pricingPage'])->name('pricing.index');
    Route::post('/pricing/update', [VendorCatalogController::class, 'updatePricing'])->name('pricing.update');

    // Categories Routes
    Route::get('/categories', [VendorCatalogController::class, 'categoriesPage'])->name('categories.index');
    Route::post('/categories/save', [VendorCatalogController::class, 'saveCategories'])->name('categories.save');
    Route::delete('/categories/{id}', [VendorCatalogController::class, 'destroyCategory'])->name('categories.destroy');

    // Inventory & Items Routes
    Route::get('/inventory', [VendorCatalogController::class, 'inventoryPage'])->name('inventory.index');
    Route::get('/items', [VendorCatalogController::class, 'inventoryPage'])->name('items');
    Route::post('/inventory/add', [VendorCatalogController::class, 'addItemsToInventory'])->name('inventory.add');
    Route::post('/items/add', [VendorCatalogController::class, 'addItemsToInventory'])->name('items.add'); 
    Route::put('/inventory/update/{id}', [VendorCatalogController::class, 'updateInventoryItem'])->name('inventory.update');
    Route::delete('/items/{id}', [VendorCatalogController::class, 'destroyItem'])->name('items.destroy');

    // Item Status Toggle & Request
    Route::post('/item/toggle-status', [VendorCatalogController::class, 'toggleItemStatus'])->name('item.toggle');
    Route::post('/item/request', [VendorCatalogController::class, 'requestNewItem'])->name('item.request');

    // QR Code Route
    Route::get('/qr-code', [VendorCatalogController::class, 'showQrCode'])->name('qrcode');

    // Smart Catalogs Routes
    Route::get('/catalogs', [CatalogController::class, 'index'])->name('catalogs.index');
    Route::post('/catalogs/store', [CatalogController::class, 'store'])->name('catalogs.store');
    Route::delete('/catalogs/{id}', [CatalogController::class, 'destroy'])->name('catalogs.destroy');
    Route::get('/catalogs/{id}/qr', [CatalogController::class, 'showQr'])->name('catalogs.qr');
    Route::post('/catalogs/order', [CatalogController::class, 'placeOrder'])->name('catalogs.order');
});