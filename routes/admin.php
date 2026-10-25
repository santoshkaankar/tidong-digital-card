<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Middleware\EnsureSuperAdmin;
use App\Http\Controllers\Admin\UserManagementController;

Route::middleware(['auth', EnsureSuperAdmin::class])->group(function () {

    Route::get('/', function () {
        $stats = [
            'users' => DB::table('users')->count(),

            'customers' => DB::table('user_account_types')
                ->join(
                    'account_types',
                    'account_types.id',
                    '=',
                    'user_account_types.account_type_id'
                )
                ->where('account_types.code', 'customer')
                ->where('user_account_types.status', 'active')
                ->count(),

            'vendors' => DB::table('vendors')
                ->whereNull('deleted_at')
                ->count(),

            'businesses' => DB::table('businesses')
                ->whereNull('deleted_at')
                ->count(),

            'branches' => DB::table('branches')
                ->whereNull('deleted_at')
                ->count(),

            'pending_vendors' => DB::table('vendors')
                ->whereNull('deleted_at')
                ->where('status', 'pending')
                ->count(),

            'pending_businesses' => DB::table('businesses')
                ->whereNull('deleted_at')
                ->where('status', 'pending')
                ->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    })->name('home');

    /*
    |--------------------------------------------------------------------------
    | Users Management
    |--------------------------------------------------------------------------
    */

    Route::get('/users', [UserManagementController::class, 'index'])
        ->name('admin.users.index');

    Route::get('/users/create', [UserManagementController::class, 'create'])
        ->name('admin.users.create');

    Route::post('/users', [UserManagementController::class, 'store'])
        ->name('admin.users.store');

    Route::get('/users/{user}', [UserManagementController::class, 'show'])
        ->name('admin.users.show');

    Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])
        ->name('admin.users.edit');

    Route::put('/users/{user}', [UserManagementController::class, 'update'])
        ->name('admin.users.update');

    Route::post('/users/{user}/deactivate', [UserManagementController::class, 'deactivate'])
        ->name('admin.users.deactivate');

    Route::post('/users/{user}/activate', [UserManagementController::class, 'activate'])
        ->name('admin.users.activate');
});
/*
/*
|--------------------------------------------------------------------------
| Vendor Management
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', EnsureSuperAdmin::class])
    ->prefix('vendors')
    ->name('admin.vendors.')
    ->controller(\App\Http\Controllers\Admin\VendorManagementController::class)
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{vendor}', 'show')->whereNumber('vendor')->name('show');
        Route::post('/{vendor}/modules/{code}', 'setModule')->whereNumber('vendor')->where('code', '[A-Za-z0-9_.-]+')->name('modules.set');
        Route::post('/{vendor}/approve', 'approve')->whereNumber('vendor')->name('approve');
        Route::post('/{vendor}/reject', 'reject')->whereNumber('vendor')->name('reject');
    });

/*
| Business approval - permissions are checked per action.
| Businesses remain order-disabled after approval.
*/
\Illuminate\Support\Facades\Route::middleware(['auth', EnsureSuperAdmin::class])
    ->prefix('businesses')
    ->name('admin.businesses.')
    ->controller(\App\Http\Controllers\Admin\BusinessManagementController::class)
    ->group(function () {
        \Illuminate\Support\Facades\Route::get('/', 'index')->name('index');
        \Illuminate\Support\Facades\Route::get('/{business}', 'show')->whereNumber('business')->name('show');
        \Illuminate\Support\Facades\Route::post('/{business}/approve', 'approve')->whereNumber('business')->name('approve');
        \Illuminate\Support\Facades\Route::post('/{business}/reject', 'reject')->whereNumber('business')->name('reject');
    });

// Platform-curated reusable items. No branch price is stored in this catalog.
Route::middleware(['auth', EnsureSuperAdmin::class])
    ->prefix('global-menu')->name('admin.global-menu.')
    ->controller(\App\Http\Controllers\Admin\GlobalMenuCatalogController::class)
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::post('/{item}/availability', 'availability')->whereNumber('item')->name('availability');
    });

// Branch and licence edits require Super Admin review before taking effect.
Route::middleware(['auth', EnsureSuperAdmin::class])
    ->prefix('branch-changes')->name('admin.branch-changes.')
    ->controller(\App\Http\Controllers\Admin\BranchChangeReviewController::class)
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{change}', 'show')->whereNumber('change')->name('show');
        Route::post('/{change}/approve', 'approve')->whereNumber('change')->name('approve');
        Route::post('/{change}/reject', 'reject')->whereNumber('change')->name('reject');
    });

// Modular admin screens (no replacement of existing admin routes).
$platformOperationsArea = 'admin';
require __DIR__.'/platform-operations.php';

// TIDONG additive multi-module integration (no existing routes removed).
require __DIR__.'/tidong_integrated_admin.php';

// Global catalog masters are platform-managed; vendor creation requires a separate future grant.
Route::middleware(['auth', EnsureSuperAdmin::class])
    ->prefix('global-masters')->name('admin.global-masters.')
    ->controller(\App\Http\Controllers\Admin\GlobalCatalogMasterController::class)
    ->group(function () {
        Route::get('/{type}', 'index')->where('type','brands|item_categories|units|variant_attributes|variant_values')->name('index');
        Route::post('/{type}', 'store')->where('type','brands|item_categories|units|variant_attributes|variant_values')->name('store');
        Route::put('/{type}/{record}', 'update')->where('type','brands|item_categories|units|variant_attributes|variant_values')->whereNumber('record')->name('update');
    });

Route::middleware(['auth', EnsureSuperAdmin::class])->prefix('global-master-permissions')
    ->name('admin.global-master-permissions.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\VendorGlobalCatalogPermissionController::class,'index'])->name('index');
        Route::put('/{vendor}', [\App\Http\Controllers\Admin\VendorGlobalCatalogPermissionController::class,'update'])->whereNumber('vendor')->name('update');
    });
