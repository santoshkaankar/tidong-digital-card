<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Vendor\VendorCardController;

Route::middleware(['web', 'auth'])->prefix('vendor/cards')->name('vendor.cards.')->group(function () {
    Route::get('/', [VendorCardController::class, 'index'])->name('index');
    Route::get('/create', [VendorCardController::class, 'create'])->name('create');
    Route::post('/save-master', [VendorCardController::class, 'storeMaster'])->name('master.save');
    Route::post('/save-view', [VendorCardController::class, 'storeCardView'])->name('view.save');
    Route::delete('/view/{id}', [VendorCardController::class, 'destroyCardView'])->name('view.delete');
    Route::get('/pincode-lookup', [VendorCardController::class, 'getPincodeDetails'])->name('pincode.lookup');
    
});

// Public Card Slug Route
Route::get('/vcard/{slug}', [VendorCardController::class, 'show'])->name('vendor.card.public');