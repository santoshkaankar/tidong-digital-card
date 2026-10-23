<?php

use App\Http\Controllers\Auth\PublicRegistrationController;
use Illuminate\Support\Facades\Route;

// Names and guest routes for existing login/register remain owned by routes/web.php.
Route::middleware(['guest', 'throttle:6,1'])->group(function (): void {
    Route::get('/forgot-password', [PublicRegistrationController::class, 'requestPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [PublicRegistrationController::class, 'sendPasswordReset'])->name('password.email');
    Route::get('/reset-password/{token}', [PublicRegistrationController::class, 'resetPasswordForm'])->name('password.reset');
    Route::post('/reset-password', [PublicRegistrationController::class, 'resetPassword'])->name('password.update');
});

Route::middleware('auth')->group(function (): void {
    Route::get('/registration/status', [PublicRegistrationController::class, 'status'])->name('registration.status');
    Route::get('/registration/delivery-profile', [\App\Http\Controllers\Delivery\DeliveryOnboardingController::class, 'edit'])->name('delivery.onboarding.edit');
    Route::post('/registration/delivery-profile', [\App\Http\Controllers\Delivery\DeliveryOnboardingController::class, 'update'])
        ->middleware('throttle:6,1')->name('delivery.onboarding.update');
    Route::post('/email/verification-notification', [PublicRegistrationController::class, 'sendVerification'])
        ->middleware('throttle:6,1')->name('verification.send');
});

// A signed, expiring email verification link also works after a fresh browser session.
Route::get('/email/verify/{id}/{hash}', [PublicRegistrationController::class, 'verifyEmail'])
    ->middleware(['signed','throttle:6,1'])->name('verification.verify');
