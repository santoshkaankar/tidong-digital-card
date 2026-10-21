<?php

use App\Http\Controllers\Vendor\OutletOpeningController;
use App\Http\Middleware\EnsureVendorAccount;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', EnsureVendorAccount::class])
    ->prefix('businesses/{business}/branches/{branch}/opening')
    ->where(['business' => '[0-9]+', 'branch' => '[0-9]+'])
    ->name('vendor.opening.')
    ->controller(OutletOpeningController::class)
    ->group(function (): void {
        Route::get('/', 'index')->name('index');
        Route::post('/begin', 'begin')->name('begin');
        Route::post('/lock', 'setLock')->name('lock');
        Route::post('/ledgers', 'createLedger')->name('ledgers.create');
        Route::post('/ledgers/attach', 'attachLedger')->name('ledgers.attach');
        Route::post('/ledgers/{ledger}/opening', 'ledgerOpening')->whereNumber('ledger')->name('ledgers.opening');
        Route::post('/stock', 'createStock')->name('stock.create');
        Route::post('/stock/{item}/opening', 'stockOpening')->whereNumber('item')->name('stock.opening');
    });
