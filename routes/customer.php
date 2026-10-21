<?php

use App\Http\Middleware\EnsureCustomerAccount;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('customer.dashboard', [
        'hasVendor' => auth()->user()->vendor()->exists(),
    ]);
})->middleware(['auth', EnsureCustomerAccount::class])
  ->name('customer.home');
