<?php
use App\Http\Controllers\Vendor\ProceedTiffinController;
use App\Http\Middleware\EnsureVendorAccount;
use App\Http\Middleware\EnsureTidongModuleEnabled;
use Illuminate\Support\Facades\Route;
// Loaded from routes/vendor.php under /vendor. All writes enforce outlet ownership.
Route::middleware(['auth',EnsureVendorAccount::class,EnsureTidongModuleEnabled::class.':proceed_tiffin'])
    ->prefix('businesses/{business}/branches/{branch}/proceed-tiffin')
    ->name('vendor.proceed-tiffin.')
    ->controller(ProceedTiffinController::class)->group(function():void{
        Route::get('/','index')->name('index');
        Route::post('/items/global','attachGlobal')->middleware('throttle:30,1')->name('items.global');
        Route::post('/items/custom','createCustom')->middleware('throttle:30,1')->name('items.custom');
        Route::post('/meals','saveMeal')->middleware('throttle:30,1')->name('meals.save');
        Route::post('/meals/{meal}/publish','publish')->whereNumber('meal')->name('meals.publish');
        Route::post('/meals/{meal}/orders','placeOrder')->whereNumber('meal')->middleware('throttle:20,1')->name('orders.store');
    });