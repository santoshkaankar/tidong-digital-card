<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\Member\CardController;
use App\Http\Controllers\Vendor\CatalogController;
use App\Http\Controllers\Customer\HubController;
use App\Http\Middleware\DeviceIdentityMiddleware;
use Illuminate\Support\Facades\View;

/*
|--------------------------------------------------------------------------
| 1. Root Route & Role Redirection
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    if (auth()->check()) {
        $role = auth()->user()->role ?? 'customer';
        return match($role) {
            'admin' => Route::has('admin.dashboard') ? redirect()->route('admin.dashboard') : redirect('/admin/dashboard'),
            'vendor', 'business' => Route::has('vendor.restaurant.dashboard') ? redirect()->route('vendor.restaurant.dashboard') : (Route::has('vendor.dashboard') ? redirect()->route('vendor.dashboard') : redirect('/')),
            'employee' => Route::has('employee.dashboard') ? redirect()->route('employee.dashboard') : redirect('/'),
            default => Route::has('member.dashboard') ? redirect()->route('member.dashboard') : redirect('/member/dashboard')
        };
    }
    return view('welcome');
});


Route::get('/instruction/{slug}', function ($slug) {
    if (View::exists('instructions.' . $slug)) {
        return view('instructions.' . $slug);
    }
    abort(404);
})->name('instruction.show');

/*
|--------------------------------------------------------------------------
| 2. System Utility Routes
|--------------------------------------------------------------------------
*/
Route::get('/fix-storage', function () {
    Artisan::call('storage:link');
    Artisan::call('view:clear');
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    return 'Storage linked and caches cleared successfully!';
});

Route::get('/change-language/{locale}', function ($locale) {
    $supportedLocales = ['en', 'hi', 'es', 'fr', 'de', 'ja', 'zh', 'ar', 'ru', 'pt', 'it', 'ko', 'bn', 'ta', 'te', 'mr', 'gu', 'kn', 'pa', 'ur'];
    if (in_array($locale, $supportedLocales)) {
        session(['locale' => $locale]);
        app()->setLocale($locale);
    }
    return redirect()->back();
})->name('change.language');

/*
|--------------------------------------------------------------------------
| 3. Auth & Public Cards Routes
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';

Route::get('/card/v/{slug}', [CardController::class, 'showPublic'])->name('card.public');
Route::get('/card/{slug}', [CardController::class, 'showPublic'])->name('card.show');
Route::get('/search-locations', [CardController::class, 'searchLocations'])->name('search.locations');
Route::get('/menu/{slug}', [MenuController::class, 'showPublicMenu'])->name('menu.public');

Route::get('/m/{slug}', [MenuController::class, 'showPublicMenu'])->name('public.menu');
Route::post('/m/{slug}/order', [MenuController::class, 'placeOrder'])->name('public.order.place');
Route::post('/order/{orderId}/complete', [MenuController::class, 'completeOrder'])->name('public.order.complete');

/*
|--------------------------------------------------------------------------
| 4. Role Group Inclusions
|--------------------------------------------------------------------------
*/
require __DIR__ . '/admin.php';

Route::prefix('member')->name('member.')->group(function () {
    require __DIR__ . '/member.php';
});

Route::prefix('employee')->name('employee.')->group(function () {
    require __DIR__ . '/employee.php';
});

/*
|--------------------------------------------------------------------------
| 5. Industry Module Routes Inclusion
|--------------------------------------------------------------------------
*/
require __DIR__ . '/payment.php';
require __DIR__ . '/emporium.php';
require __DIR__ . '/hotel.php';
require __DIR__ . '/money_exchange.php';
require __DIR__ . '/tourist_guide.php';
require __DIR__ . '/restaurant.php';
require __DIR__ . '/taxi.php';
require __DIR__ .'/retail.php';
/*
|--------------------------------------------------------------------------
| 6. Public Guest Orders & Hub Routes
|--------------------------------------------------------------------------
*/
Route::get('/c/{slug}', [CatalogController::class, 'showPublicCatalog'])->name('catalogs.public');
Route::post('/menu/{slug}/order', [CatalogController::class, 'placeOrder'])->name('menu.order');
Route::get('/guest/order/{orderId}', [CatalogController::class, 'guestOrderStatus'])->name('guest.order.status');
Route::post('/guest/order/vacate/{orderId}', [CatalogController::class, 'vacateGuestTable'])->name('guest.order.vacate');

Route::middleware([DeviceIdentityMiddleware::class])->group(function () {
    Route::get('/hub', [HubController::class, 'index'])->name('customer.hub');
});

/*
|--------------------------------------------------------------------------
| 7. Public Content & Policy Pages
|--------------------------------------------------------------------------
*/
Route::view('/about-us', 'pages.about')->name('pages.about');
Route::view('/terms-and-conditions', 'pages.terms')->name('pages.terms');
Route::view('/privacy-policy', 'pages.privacy')->name('pages.privacy');
Route::view('/contact-us', 'pages.contact')->name('pages.contact');
Route::view('/affiliate-program', 'pages.affiliate')->name('pages.affiliate');
Route::view('/royalty-program', 'pages.royalty')->name('pages.royalty');
Route::view('/luckydrow', 'pages.luckydrow')->name('pages.luckydrow');
/*
|--------------------------------------------------------------------------
| 8. Guidance Pages
|--------------------------------------------------------------------------
*/
Route::prefix('guidance')->group(function () {
    Route::get('/member', function () { return view('pages.guidance.member'); })->name('guidance.member');
    Route::get('/restaurant', function () { return view('pages.guidance.restaurant'); })->name('guidance.restaurant');
});

/*
|--------------------------------------------------------------------------
| 9. Alias & Service Fallback Routes (Prevents RouteNotFoundExceptions)
|--------------------------------------------------------------------------
*/
// Vendor Dashboard Alias
Route::get('/vendor/dashboard', function () {
    if (Route::has('vendor.restaurant.dashboard')) {
        return redirect()->route('vendor.restaurant.dashboard');
    }
    return redirect()->route('member.dashboard');
})->name('vendor.dashboard');

// Quick Services Menu Links
Route::get('/services/taxi', function () { return redirect()->route('customer.hub'); })->name('vendor.taxi.rides');
Route::get('/services/forex', function () { return redirect()->route('customer.hub'); })->name('vendor.exchange.rates');
Route::get('/services/guide', function () { return redirect()->route('customer.hub'); })->name('vendor.guide.bookings');