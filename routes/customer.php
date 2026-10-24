<?php

use App\Http\Controllers\Customer\CustomerAddressController;
use App\Http\Controllers\Customer\CustomerDashboardController;
use App\Http\Controllers\Customer\CustomerExploreController;
use App\Http\Controllers\Customer\CustomerOrdersController;
use App\Http\Controllers\Customer\CustomerPreferencesController;
use App\Http\Controllers\Customer\CustomerProfileController;
use App\Http\Controllers\Customer\CustomerSecurityController;
use App\Http\Middleware\EnsureCustomerAccount;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', EnsureCustomerAccount::class])
    ->name('customer.')
    ->group(function () {
        Route::get('/', CustomerDashboardController::class)->name('home');
        Route::get('/explore', [CustomerExploreController::class, 'index'])->name('explore.index');
        Route::get('/explore/{service}', [CustomerExploreController::class, 'service'])
            ->where('service', '[a-z_]+')->name('explore.service');
        Route::get('/orders', CustomerOrdersController::class)->name('orders.index');
        Route::get('/profile', [CustomerProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [CustomerProfileController::class, 'update'])->middleware('throttle:12,1')->name('profile.update');
        Route::get('/preferences', [CustomerPreferencesController::class, 'edit'])->name('preferences.edit');
        Route::put('/preferences', [CustomerPreferencesController::class, 'update'])->middleware('throttle:12,1')->name('preferences.update');
        Route::get('/security', [CustomerSecurityController::class, 'edit'])->name('security.edit');
        Route::put('/security/password', [CustomerSecurityController::class, 'updatePassword'])->middleware('throttle:5,1')->name('security.password');

        Route::get('/assistant/food', [\App\Http\Controllers\Customer\AiRestaurantAssistantController::class, 'index'])->name('ai-restaurant.index');
        Route::post('/assistant/food/new-chat', [\App\Http\Controllers\Customer\AiRestaurantAssistantController::class, 'newChat'])->name('ai-restaurant.new-chat');
        Route::post('/assistant/food', [\App\Http\Controllers\Customer\AiRestaurantAssistantController::class, 'ask'])->middleware('throttle:4,1')->name('ai-restaurant.ask');

        Route::get('/addresses', [CustomerAddressController::class, 'index'])->name('addresses.index');
        Route::get('/addresses/create', [CustomerAddressController::class, 'create'])->name('addresses.create');
        Route::post('/addresses', [CustomerAddressController::class, 'store'])->middleware('throttle:12,1')->name('addresses.store');
        Route::get('/addresses/{address}/edit', [CustomerAddressController::class, 'edit'])->whereNumber('address')->name('addresses.edit');
        Route::put('/addresses/{address}', [CustomerAddressController::class, 'update'])->middleware('throttle:12,1')->whereNumber('address')->name('addresses.update');
        Route::delete('/addresses/{address}', [CustomerAddressController::class, 'destroy'])->middleware('throttle:12,1')->whereNumber('address')->name('addresses.destroy');
        Route::post('/addresses/{address}/default', [CustomerAddressController::class, 'makeDefault'])->middleware('throttle:12,1')->whereNumber('address')->name('addresses.default');
    });
