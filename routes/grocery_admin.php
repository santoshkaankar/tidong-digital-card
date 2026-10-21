<?php

use App\Http\Controllers\Admin\GroceryFeatureController;
use App\Http\Middleware\EnsureSuperAdmin;
use Illuminate\Support\Facades\Route;

// Included from routes/admin.php, already mounted under /admin.
Route::middleware(['auth', EnsureSuperAdmin::class])
    ->prefix('grocery/features')->name('admin.grocery.features.')
    ->controller(GroceryFeatureController::class)->group(function (): void {
        Route::get('/', 'index')->name('index');
        Route::post('/{feature}', 'update')->where('feature', '[a-z_]+')->name('update');
    });
