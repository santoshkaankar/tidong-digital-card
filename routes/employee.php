<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'role:employee'])->group(function () {
    Route::get('/dashboard', function () {
        return view('employee.dashboard');
    })->name('dashboard');
});