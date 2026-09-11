<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Vendor\Tourist_Guide\TouristGuideController;

// Prefix aur Name ko 'vendor.guide.' set kar diya hai taaki welcome view matches clear ho sakein
Route::middleware(['auth', 'role:vendor'])
    ->prefix('vendor/tourist-guide')
    ->name('vendor.guide.')
    ->group(function () {
        Route::get('/dashboard', [TouristGuideController::class, 'dashboard'])->name('dashboard');
        Route::get('/bookings', [TouristGuideController::class, 'dashboard'])->name('bookings'); // Welcome view ke liye booking route alias
        Route::post('/store', [TouristGuideController::class, 'store'])->name('store');
    });