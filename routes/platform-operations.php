<?php

use App\Http\Controllers\Admin\DeliveryManagementController;
use App\Http\Controllers\Admin\EmployeeCapabilityController;
use App\Http\Controllers\Admin\ModuleManagementController;
use App\Http\Middleware\EnsurePlatformCapability as Capability;
use App\Http\Middleware\EnsurePlatformSupervisor;
use Illuminate\Support\Facades\Route;

// This file is included under three separately guarded admin-area prefixes.
$area = $platformOperationsArea;
Route::middleware(['auth'])->group(function () use ($area): void {
    Route::prefix('modules')->name($area.'.modules.')->group(function (): void {
        Route::get('/', [ModuleManagementController::class, 'index'])->middleware(Capability::class.':modules.view')->name('index');
        Route::post('/{code}/register', [ModuleManagementController::class, 'register'])->middleware(Capability::class.':modules.register')->name('register');
        Route::post('/{code}/toggle', [ModuleManagementController::class, 'toggle'])->middleware(Capability::class.':modules.toggle')->name('toggle');
        Route::post('/features/{code}/toggle', [ModuleManagementController::class, 'toggleFeature'])
            ->where('code', '[A-Za-z0-9_.-]+')->middleware(Capability::class.':features.toggle')->name('features.toggle');
    });
    Route::get('/delivery', [DeliveryManagementController::class, 'index'])
        ->middleware(Capability::class.':delivery.diagnostics.view')->name($area.'.delivery.index');
    // NO platform routes for dispatch, business cancellations, partner approval, prices or payments.
    Route::middleware(EnsurePlatformSupervisor::class)->prefix('employee-permissions')
        ->name($area.'.employee-permissions.')->group(function (): void {
            Route::get('/', [EmployeeCapabilityController::class, 'index'])->name('index');
            Route::post('/', [EmployeeCapabilityController::class, 'update'])->name('update');
        });
});
