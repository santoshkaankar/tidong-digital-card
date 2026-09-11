<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Vendor\Emporium\EmporiumController;

Route::middleware(['auth', 'role:vendor'])
    ->prefix('vendor/emporium')
    ->name('vendor.emporium.')
    ->group(function () {
        Route::get('/dashboard', [EmporiumController::class, 'dashboard'])->name('dashboard');
        Route::post('/store', [EmporiumController::class, 'store'])->name('store');
    });