<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\CustomerAuthController;

// Homepage — पुराना homepage सुरक्षित रखा गया है
Route::get('/', function () {
    return view('welcome');
});

// Login और Registration — केवल वे लोग जो लॉगिन नहीं हैं
Route::middleware('guest')->group(function () {
    Route::get('/login', [CustomerAuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [CustomerAuthController::class, 'login'])
        ->middleware('throttle:login')
        ->name('login.store');

    Route::get('/register', [\App\Http\Controllers\Auth\PublicRegistrationController::class, 'show'])
        ->name('register');

    Route::post('/register', [\App\Http\Controllers\Auth\PublicRegistrationController::class, 'store'])
        ->name('register.store');
});

// Logout — केवल लॉगिन किए हुए यूज़र
Route::post('/logout', [CustomerAuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Public, read-only menu accessed using a secret per-table QR token.
Route::get('/menu/{token}', [\App\Http\Controllers\PublicQrMenuController::class, 'show'])->name('qr.menu.show');

Route::post('/menu/{token}/request', [\App\Http\Controllers\RestaurantQrOrderController::class, 'submit'])
    ->middleware('throttle:6,1')->name('qr.menu.submit');

// TIDONG additive multi-module integration (no existing routes removed).
require __DIR__.'/tidong_integrated_web.php';

// Customer personalized-gift catalogue and order tracking.
require __DIR__.'/sublimation_customer.php';

// Registration-only additive routes.
require __DIR__.'/public_registration.php';

// Public Terms and Conditions: adapted from owner-supplied legacy page.
Route::view('/terms-and-conditions', 'pages.terms')->name('pages.terms');
