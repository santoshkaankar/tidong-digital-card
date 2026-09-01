<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\Member\CardController;
use App\Http\Controllers\Vendor\CatalogController;

// Utility & System Routes
Route::get('/fix-storage', function () {
    Artisan::call('storage:link');
    Artisan::call('view:clear');
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    return 'Storage linked and caches cleared successfully!';
});

// Load Auth Routes
require __DIR__ . '/auth.php';

// Common Public Routes
Route::get('/card/v/{slug}', [CardController::class, 'showPublic'])->name('card.public');
Route::get('/card/{slug}', [CardController::class, 'showPublic'])->name('card.show');
Route::get('/search-locations', [CardController::class, 'searchLocations'])->name('search.locations');
Route::get('/menu/{slug}', [MenuController::class, 'showPublicMenu'])->name('menu.public');
Route::post('/order/{orderId}/complete', [MenuController::class, 'completeOrder'])->name('order.complete');

// Customer Public Scan Routes
Route::get('/m/{slug}', [MenuController::class, 'showPublicMenu'])->name('public.menu');
Route::post('/m/{slug}/order', [MenuController::class, 'placeOrder'])->name('public.order.place');
Route::post('/order/{orderId}/complete', [MenuController::class, 'completeOrder'])->name('public.order.complete');

// Authenticated Role Routes Inclusion
require __DIR__ . '/admin.php';

Route::prefix('vendor')->name('vendor.')->group(function () {
    require __DIR__ . '/vendor.php';
});

Route::prefix('member')->name('member.')->group(function () {
    require __DIR__ . '/member.php';
});

Route::prefix('employee')->name('employee.')->group(function () {
    require __DIR__ . '/employee.php';
});

// Public Guest Order Routes (CatalogController से लिंक्ड)
Route::get('/c/{slug}', [CatalogController::class, 'showPublicCatalog'])->name('catalogs.public');
Route::post('/menu/{slug}/order', [CatalogController::class, 'placeOrder'])->name('menu.order');
Route::get('/guest/order/{orderId}', [CatalogController::class, 'guestOrderStatus'])->name('guest.order.status');
Route::post('/guest/order/vacate/{orderId}', [CatalogController::class, 'vacateGuestTable'])->name('guest.order.vacate');