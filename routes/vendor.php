<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Vendor\VendorController;
use App\Http\Controllers\Vendor\VendoritemController;
use App\Http\Controllers\Vendor\CatalogController;
use App\Http\Controllers\Vendor\VendorDashboardController;

// Auth Middleware के अंदर सभी Vendor Routes
Route::middleware(['auth'])->group(function () {
    
    // Business Dashboard Route (अब URL बनेगा: /vendor/dashboard)
    Route::get('/dashboard', [VendorDashboardController::class, 'index'])->name('dashboard');

    // Kitchen Dashboard Route
    Route::get('/kitchen', [VendoritemController::class, 'kitchenDashboard'])->name('kitchen.orders');
    Route::get('/kitchen/live', [VendoritemController::class, 'kitchenDashboard'])->name('kitchen.dashboard');
    
    // Order Complete Route
    Route::post('/kitchen/order/complete/{orderId}', [VendoritemController::class, 'completeOrder'])->name('public.order.complete');

    // Pricing Routes
    Route::get('/pricing', [VendorController::class, 'pricingPage'])->name('pricing.index');
    Route::post('/pricing/update', [VendorController::class, 'updatePricing'])->name('pricing.update');

    // Categories Routes
    Route::get('/categories', [VendorController::class, 'categoriesPage'])->name('categories.index');
    Route::post('/categories/save', [VendorController::class, 'saveCategories'])->name('categories.save');
    Route::delete('/categories/{id}', [VendorController::class, 'destroyCategory'])->name('categories.destroy');

    // Inventory Routes
    Route::get('/inventory', [VendorController::class, 'inventoryPage'])->name('inventory.index');
    Route::get('/items', [VendorController::class, 'inventoryPage'])->name('items');
    Route::post('/inventory/add', [VendorController::class, 'addItemsToInventory'])->name('inventory.add');
    Route::post('/items/add', [VendorController::class, 'addItemsToInventory'])->name('items.add'); 
    Route::put('/inventory/update/{id}', [VendorController::class, 'updateInventoryItem'])->name('inventory.update');
    Route::delete('/items/{id}', [VendorController::class, 'destroyItem'])->name('items.destroy');

    // Item Status Toggle & Request
    Route::post('/item/toggle-status', [VendorController::class, 'toggleItemStatus'])->name('item.toggle');
    Route::post('/item/request', [VendorController::class, 'requestNewItem'])->name('item.request');

    // QR Code
    Route::get('/qr-code', [VendorController::class, 'showQrCode'])->name('qrcode');

    // Smart Catalogs Routes
    Route::get('/catalogs', [CatalogController::class, 'index'])->name('catalogs.index');
    Route::post('/catalogs/store', [CatalogController::class, 'store'])->name('catalogs.store');
    Route::delete('/catalogs/{id}', [CatalogController::class, 'destroy'])->name('catalogs.destroy');
    Route::get('/catalogs/{id}/qr', [CatalogController::class, 'showQr'])->name('catalogs.qr');
    Route::post('/catalogs/order', [CatalogController::class, 'placeOrder'])->name('catalogs.order');
});