<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Middleware\EnsureManagedPlatformUser;
use App\Http\Middleware\EnsurePermission;

Route::middleware([
    'auth',
    EnsureManagedPlatformUser::class . ':admin',
])->group(function () {
    Route::get('/', function () {
        $stats = [
            'users' => DB::table('users')->count(),
            'vendors' => DB::table('vendors')
                ->whereNull('deleted_at')
                ->count(),
            'businesses' => DB::table('businesses')
                ->whereNull('deleted_at')
                ->count(),
        ];

        return view('staff-admin.dashboard', compact('stats'));
    })->name('staff-admin.home');
});
/*
|--------------------------------------------------------------------------
| Vendor Management - each action requires its own permission
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', EnsureManagedPlatformUser::class . ':admin'])
    ->prefix('vendors')
    ->name('staff-admin.vendors.')
    ->controller(\App\Http\Controllers\Admin\VendorManagementController::class)
    ->group(function () {
        Route::get('/', 'index')
            ->middleware(EnsurePermission::class . ':vendors.view')
            ->name('index');

        Route::get('/{vendor}', 'show')
            ->whereNumber('vendor')
            ->middleware(EnsurePermission::class . ':vendors.view')
            ->name('show');

        Route::post('/{vendor}/approve', 'approve')
            ->whereNumber('vendor')
            ->middleware(EnsurePermission::class . ':vendors.approve')
            ->name('approve');

        Route::post('/{vendor}/reject', 'reject')
            ->whereNumber('vendor')
            ->middleware(EnsurePermission::class . ':vendors.reject')
            ->name('reject');
    });

/*
| Business approval - permissions are checked per action.
| Businesses remain order-disabled after approval.
*/
\Illuminate\Support\Facades\Route::middleware(['auth', EnsureManagedPlatformUser::class . ':admin'])
    ->prefix('businesses')
    ->name('staff-admin.businesses.')
    ->controller(\App\Http\Controllers\Admin\BusinessManagementController::class)
    ->group(function () {
        \Illuminate\Support\Facades\Route::get('/', 'index')->middleware(\App\Http\Middleware\EnsurePermission::class . ':businesses.view')->name('index');
        \Illuminate\Support\Facades\Route::get('/{business}', 'show')->whereNumber('business')->middleware(\App\Http\Middleware\EnsurePermission::class . ':businesses.view')->name('show');
        \Illuminate\Support\Facades\Route::post('/{business}/approve', 'approve')->whereNumber('business')->middleware(\App\Http\Middleware\EnsurePermission::class . ':businesses.approve')->name('approve');
        \Illuminate\Support\Facades\Route::post('/{business}/reject', 'reject')->whereNumber('business')->middleware(\App\Http\Middleware\EnsurePermission::class . ':businesses.reject')->name('reject');
    });

// Permission-controlled branch change review. No action edits a Branch without approval.
Route::middleware(['auth', EnsureManagedPlatformUser::class . ':admin'])
    ->prefix('branch-changes')->name('staff-admin.branch-changes.')
    ->controller(\App\Http\Controllers\Admin\BranchChangeReviewController::class)
    ->group(function () {
        Route::get('/', 'index')->middleware(EnsurePermission::class . ':approvals.view')->name('index');
        Route::get('/{change}', 'show')->whereNumber('change')->middleware(EnsurePermission::class . ':approvals.view')->name('show');
        Route::post('/{change}/approve', 'approve')->whereNumber('change')->middleware(EnsurePermission::class . ':approvals.approve')->name('approve');
        Route::post('/{change}/reject', 'reject')->whereNumber('change')->middleware(EnsurePermission::class . ':approvals.reject')->name('reject');
    });

$platformOperationsArea = 'staff-admin';
require __DIR__.'/platform-operations.php';
