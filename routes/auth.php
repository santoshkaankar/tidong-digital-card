<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    if (auth()->check()) {
        $role = auth()->user()->role;
        if ($role === 'admin' && \Route::has('admin.dashboard')) return redirect()->route('admin.dashboard');
        if ($role === 'employee' && \Route::has('employee.dashboard')) return redirect()->route('employee.dashboard');
        if ($role === 'vendor' && \Route::has('vendor.dashboard')) return redirect()->route('vendor.dashboard');
        
        // Safe check for member dashboard
        if (\Route::has('member.dashboard')) {
            return redirect()->route('member.dashboard');
        }
        
        return view('member.dashboard'); // Fallback agar route register na ho
    }
    return view('welcome');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');