<?php

use App\Http\Controllers\Kds\KdsController;
use App\Http\Middleware\EnsureVendorAccount;
use Illuminate\Support\Facades\Route;

// KDS v1: Vendor owner only. Staff permissions will be added separately.
Route::middleware(['auth', EnsureVendorAccount::class])
    ->controller(KdsController::class)->group(function () {
        Route::get('/', 'index')->name('kds.home');
        Route::get('/branches/{branch}', 'showBranch')->whereNumber('branch')->name('kds.branches.show');
        Route::post('/orders/{order}/status', 'updateStatus')->whereNumber('order')->name('kds.orders.status');
        Route::post('/orders/{order}/free-ask', 'freeAsk')->whereNumber('order')->middleware('throttle:12,1')->name('kds.orders.free-ask');
        Route::post('/items/{item}/progress', 'itemProgress')->whereNumber('item')->middleware('throttle:20,1')->name('kds.items.progress');
        Route::get('/branches/{branch}/tables/{table}/summary', 'tableSummary')->whereNumber('branch')->whereNumber('table')->middleware('throttle:30,1')->name('kds.tables.summary');
    });
