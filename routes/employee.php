<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\EnsureManagedPlatformUser;
use App\Http\Middleware\EnsurePermission;
use App\Http\Controllers\Employee\FinanceLedgerGroupController;

Route::middleware([
    'auth',
    EnsureManagedPlatformUser::class . ':platform_employee',
])->group(function () {

    Route::get('/', function () {
        return view('employee.dashboard');
    })->name('employee.home');

    Route::get('/finance', function () {
        return view('finance.index');
    })
        ->middleware(EnsurePermission::class . ':finance.view')
        ->name('employee.finance.index');

    Route::get('/finance/manage', function () {
        return view('finance.manage');
    })
        ->middleware(EnsurePermission::class . ':finance.manage')
        ->name('employee.finance.manage');

    Route::get('/finance/groups', [FinanceLedgerGroupController::class, 'index'])
        ->middleware(EnsurePermission::class . ':finance.manage')
        ->name('employee.finance.groups.index');

    Route::post('/finance/groups', [FinanceLedgerGroupController::class, 'store'])
        ->middleware(EnsurePermission::class . ':finance.manage')
        ->name('employee.finance.groups.store');
});
/*
|--------------------------------------------------------------------------
| Permission-protected Vendor Management
|--------------------------------------------------------------------------
*/
Route::middleware([
    'auth',
    EnsureManagedPlatformUser::class . ':platform_employee',
])->prefix('vendors')
  ->name('employee.vendors.')
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
\Illuminate\Support\Facades\Route::middleware(['auth', EnsureManagedPlatformUser::class . ':platform_employee'])
    ->prefix('businesses')
    ->name('employee.businesses.')
    ->controller(\App\Http\Controllers\Admin\BusinessManagementController::class)
    ->group(function () {
        \Illuminate\Support\Facades\Route::get('/', 'index')->middleware(\App\Http\Middleware\EnsurePermission::class . ':businesses.view')->name('index');
        \Illuminate\Support\Facades\Route::get('/{business}', 'show')->whereNumber('business')->middleware(\App\Http\Middleware\EnsurePermission::class . ':businesses.view')->name('show');
        \Illuminate\Support\Facades\Route::post('/{business}/approve', 'approve')->whereNumber('business')->middleware(\App\Http\Middleware\EnsurePermission::class . ':businesses.approve')->name('approve');
        \Illuminate\Support\Facades\Route::post('/{business}/reject', 'reject')->whereNumber('business')->middleware(\App\Http\Middleware\EnsurePermission::class . ':businesses.reject')->name('reject');
    });

// Permission-controlled branch change review. No action edits a Branch without approval.
Route::middleware(['auth', EnsureManagedPlatformUser::class . ':platform_employee'])
    ->prefix('branch-changes')->name('employee.branch-changes.')
    ->controller(\App\Http\Controllers\Admin\BranchChangeReviewController::class)
    ->group(function () {
        Route::get('/', 'index')->middleware(EnsurePermission::class . ':approvals.view')->name('index');
        Route::get('/{change}', 'show')->whereNumber('change')->middleware(EnsurePermission::class . ':approvals.view')->name('show');
        Route::post('/{change}/approve', 'approve')->whereNumber('change')->middleware(EnsurePermission::class . ':approvals.approve')->name('approve');
        Route::post('/{change}/reject', 'reject')->whereNumber('change')->middleware(EnsurePermission::class . ':approvals.reject')->name('reject');
    });

$platformOperationsArea = 'employee';
require __DIR__.'/platform-operations.php';
