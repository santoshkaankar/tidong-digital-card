<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\Member\CardController;
use App\Http\Controllers\Vendor\CatalogController;
use App\Http\Controllers\Customer\HubController;
use App\Http\Middleware\DeviceIdentityMiddleware;

// Utility & System Routes
Route::get('/fix-storage', function () {
    Artisan::call('storage:link');
    Artisan::call('view:clear');
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    return 'Storage linked and caches cleared successfully!';
});

// Universal 20-Language Switcher (Connected with SetLocaleMiddleware)
Route::get('/change-language/{locale}', function ($locale) {
    $supportedLocales = [
        'en', 'hi', 'es', 'fr', 'de', 'ja', 'zh', 'ar', 'ru', 'pt', 
        'it', 'ko', 'bn', 'ta', 'te', 'mr', 'gu', 'kn', 'pa', 'ur'
    ];
    if (in_array($locale, $supportedLocales)) {
        session(['locale' => $locale]);
        app()->setLocale($locale);
    }
    return redirect()->back();
})->name('change.language');

// Load Auth Routes
require __DIR__ . '/auth.php';

// Common Public Routes
Route::get('/card/v/{slug}', [CardController::class, 'showPublic'])->name('card.public');
Route::get('/card/{slug}', [CardController::class, 'showPublic'])->name('card.show');
Route::get('/search-locations', [CardController::class, 'searchLocations'])->name('search.locations');
Route::get('/menu/{slug}', [MenuController::class, 'showPublicMenu'])->name('menu.public');

// Customer Public Scan Routes
Route::get('/m/{slug}', [MenuController::class, 'showPublicMenu'])->name('public.menu');
Route::post('/m/{slug}/order', [MenuController::class, 'placeOrder'])->name('public.order.place');
Route::post('/order/{orderId}/complete', [MenuController::class, 'completeOrder'])->name('public.order.complete');

// Authenticated Role Routes Inclusion
require __DIR__ . '/admin.php';

Route::prefix('member')->name('member.')->group(function () {
    require __DIR__ . '/member.php';
});

Route::prefix('employee')->name('employee.')->group(function () {
    require __DIR__ . '/employee.php';
});

// All Industry Modules Loaded
require __DIR__ . '/payment.php';
require __DIR__ . '/emporium.php';
require __DIR__ . '/hotel.php';
require __DIR__ . '/money_exchange.php';
require __DIR__ . '/tourist_guide.php';
require __DIR__ . '/restaurant.php';
require __DIR__ . '/taxi.php';

// Public Guest Order Routes (CatalogController)
Route::get('/c/{slug}', [CatalogController::class, 'showPublicCatalog'])->name('catalogs.public');
Route::post('/menu/{slug}/order', [CatalogController::class, 'placeOrder'])->name('menu.order');
Route::get('/guest/order/{orderId}', [CatalogController::class, 'guestOrderStatus'])->name('guest.order.status');
Route::post('/guest/order/vacate/{orderId}', [CatalogController::class, 'vacateGuestTable'])->name('guest.order.vacate');

// Super-QR Universal Hub (Device Fingerprint Enabled)
Route::middleware([DeviceIdentityMiddleware::class])->group(function () {
    Route::get('/hub', [HubController::class, 'index'])->name('customer.hub');
});

// Public Pages Routes
Route::view('/about-us', 'pages.about')->name('pages.about');
Route::view('/terms-and-conditions', 'pages.terms')->name('pages.terms');
Route::view('/privacy-policy', 'pages.privacy')->name('pages.privacy');
Route::view('/contact-us', 'pages.contact')->name('pages.contact');

// Guidance Pages
Route::prefix('guidance')->group(function () {
    Route::get('/member', function () {
        return view('pages.guidance.member');
    })->name('guidance.member');

    Route::get('/restaurant', function () {
        return view('pages.guidance.restaurant');
    })->name('guidance.restaurant');
});