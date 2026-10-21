<?php

use App\Http\Controllers\Pos\PosController;
use App\Http\Middleware\EnsureVendorAccount;
use Illuminate\Support\Facades\Route;

// Global Item selection and Branch-only custom items; existing POS order routes unchanged.
Route::middleware(['auth', EnsureVendorAccount::class])
    ->controller(\App\Http\Controllers\Pos\BranchMenuCatalogController::class)
    ->group(function () {
        Route::get('/branches/{branch}/menu-library','index')->whereNumber('branch')->name('pos.menu-library.index');
        Route::post('/branches/{branch}/menu-library/global','attachGlobal')->whereNumber('branch')->name('pos.menu-library.global');
        Route::post('/branches/{branch}/menu-library/custom','createCustom')->whereNumber('branch')->name('pos.menu-library.custom');
        Route::post('/menu/{item}/photo','updateCustomPhoto')->whereNumber('item')->name('pos.menu-library.photo');
    });

// POS v1: authenticated Vendor owner, not public access.
Route::middleware(['auth', EnsureVendorAccount::class])
    ->controller(PosController::class)->group(function () {
        Route::get('/', 'index')->name('pos.home');
        Route::get('/branches/{branch}', 'showBranch')->whereNumber('branch')->name('pos.branches.show');
        Route::post('/businesses/{business}/accepting', 'businessAccepting')->whereNumber('business')->name('pos.businesses.accepting');
        Route::post('/branches/{branch}/accepting', 'branchAccepting')->whereNumber('branch')->name('pos.branches.accepting');
        Route::post('/branches/{branch}/menu', 'addMenuItem')->whereNumber('branch')->name('pos.menu.store');
        Route::post('/menu/{item}/availability', 'menuAvailability')->whereNumber('item')->name('pos.menu.availability');
        Route::post('/branches/{branch}/orders', 'placeOrder')->whereNumber('branch')->name('pos.orders.store');
        Route::post('/orders/{order}/cancel', 'cancelOrder')->whereNumber('order')->name('pos.orders.cancel');
    });

Route::middleware(['auth', EnsureVendorAccount::class])
    ->controller(\App\Http\Controllers\Pos\DiningTableController::class)->group(function () {
        Route::get('/branches/{branch}/tables','index')->whereNumber('branch')->name('pos.tables.index');
        Route::post('/branches/{branch}/tables','store')->whereNumber('branch')->name('pos.tables.store');
        Route::post('/tables/{table}/toggle','toggle')->whereNumber('table')->name('pos.tables.toggle');
    });

// Owner-only review queue for unconfirmed QR guest requests.
Route::middleware(['auth', EnsureVendorAccount::class])
    ->controller(\App\Http\Controllers\Pos\DiningQrRequestsController::class)->group(function () {
        Route::get('/branches/{branch}/qr-requests','index')->whereNumber('branch')->name('pos.qr.requests');
        Route::post('/qr-requests/{qrRequest}/accept','accept')->whereNumber('qrRequest')->name('pos.qr.accept');
        Route::post('/qr-requests/{qrRequest}/reject','reject')->whereNumber('qrRequest')->name('pos.qr.reject');
    });
