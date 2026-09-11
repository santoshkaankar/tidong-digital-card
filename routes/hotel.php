<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Vendor\Hotel\HotelController;

Route::middleware(['auth', 'role:vendor'])
    ->prefix('vendor/hotel')
    ->name('vendor.hotel.')
    ->group(function () {
        Route::get('/dashboard', [HotelController::class, 'dashboard'])->name('dashboard');
        Route::post('/store', [HotelController::class, 'store'])->name('store');
    });