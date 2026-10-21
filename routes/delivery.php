<?php

use App\Http\Controllers\Delivery\DeliveryPartnerController;
use App\Http\Middleware\EnsureDeliveryPartner;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', EnsureDeliveryPartner::class])->group(function (): void {
    Route::get('/', [DeliveryPartnerController::class,'index'])->name('delivery.home');
    Route::post('/availability',[DeliveryPartnerController::class,'availability'])
        ->name('delivery.availability');
    Route::post('/jobs/{job}/status',[DeliveryPartnerController::class,'update'])
        ->middleware('throttle:6,1')->whereNumber('job')->name('delivery.jobs.status');
});
