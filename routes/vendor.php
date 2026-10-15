<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Vendor\VendorCardController;
use App\Http\Controllers\Vendor\VendorBusinessController;
use App\Http\Controllers\Vendor\VendorDashboardController;

Route::middleware(['auth'])->prefix('vendor')->name('vendor.')->group(function () {
    // Master Dashboard
    Route::get('/dashboard', [VendorDashboardController::class, 'index'])->name('dashboard');
    
    // Business management routes
    Route::get('/businesses', [VendorBusinessController::class, 'index'])->name('businesses.index');
    Route::post('/businesses', [VendorBusinessController::class, 'store'])->name('businesses.store');
    Route::get('/businesses/switch/{id}', [VendorBusinessController::class, 'switchBusiness'])->name('businesses.switch');
});

// Vendor Cards Routes
Route::middleware(['web', 'auth', 'role:vendor,business'])->prefix('vendor/cards')->name('vendor.card.')->group(function () {
    Route::get('/', [VendorCardController::class, 'index'])->name('index');
    Route::get('/create', [VendorCardController::class, 'create'])->name('create');
    Route::post('/save-master', [VendorCardController::class, 'storeMaster'])->name('master.save');
    Route::post('/save-view', [VendorCardController::class, 'storeCardView'])->name('view.save');
    Route::delete('/view/{id}', [VendorCardController::class, 'destroyCardView'])->name('view.delete');
    Route::get('/pincode-lookup', [VendorCardController::class, 'getPincodeDetails'])->name('pincode.lookup');
});

// Public Card Slug Route
Route::get('/vcard/{slug}', [VendorCardController::class, 'show'])->name('vendor.card.public');

// Vendor Businesses Management (Multi-Business per Vendor)
Route::middleware(['web', 'auth'])->prefix('businesses')->name('businesses.')->group(function () {
    Route::get('/', [VendorBusinessController::class, 'index'])->name('index');
    Route::post('/store', [VendorBusinessController::class, 'store'])->name('store');
    Route::get('/switch/{id}', [VendorBusinessController::class, 'switchBusiness'])->name('switch');
});