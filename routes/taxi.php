<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Vendor\Taxi\TaxiController;

Route::middleware(['auth', 'vendor'])->prefix('vendor/taxi')->name('vendor.taxi.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [TaxiController::class, 'index'])->name('dashboard');
    
    // Taxi & Vehicle Management
    Route::get('/vehicles', [TaxiController::class, 'index'])->name('vehicles.index');
    Route::post('/vehicles/store', [TaxiController::class, 'store'])->name('vehicles.store');
    
    // Bookings & Rides Management
    Route::get('/bookings', [TaxiController::class, 'bookings'])->name('bookings.index');
    Route::get('/rides', [TaxiController::class, 'bookings'])->name('rides'); // Added alias to match views
    Route::post('/bookings/{id}/status', [TaxiController::class, 'updateBookingStatus'])->name('bookings.updateStatus');
    
    // Documents Management
    Route::get('/documents', [TaxiController::class, 'documents'])->name('documents.index');
});