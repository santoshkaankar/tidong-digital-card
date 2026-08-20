<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    if (auth()->check()) {
        $role = auth()->user()->role;
        if ($role === 'admin' && \Route::has('admin.dashboard')) return redirect()->route('admin.dashboard');
        if ($role === 'employee' && \Route::has('employee.dashboard')) return redirect()->route('employee.dashboard');
        if ($role === 'vendor' && \Route::has('vendor.dashboard')) return redirect()->route('vendor.dashboard');
        
        if (\Route::has('member.dashboard')) {
            return redirect()->route('member.dashboard');
        }
        
        return view('member.dashboard');
    }
    return view('welcome');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Password Reset Complete Routes
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');