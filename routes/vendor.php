<?php

use App\Http\Controllers\Vendor\BranchLicenseController;
use App\Http\Controllers\Vendor\VendorOnboardingController;
use App\Http\Controllers\Vendor\VendorWorkspaceController;
use App\Http\Middleware\EnsureCustomerAccount;
use App\Http\Middleware\EnsureVendorAccount;
use Illuminate\Support\Facades\Route;

// Customers can apply to be Vendors using their existing account.
Route::middleware(['auth', EnsureCustomerAccount::class])->group(function () {
    Route::get('/apply', [VendorOnboardingController::class, 'create'])
        ->name('vendor.apply');
    Route::post('/apply', [VendorOnboardingController::class, 'store'])
        ->name('vendor.apply.store');
});

// Vendor-only area: all Business and Branch writes are owner-scoped.
Route::middleware(['auth', EnsureVendorAccount::class])->group(function () {
    Route::get('/', [VendorWorkspaceController::class, 'index'])->name('vendor.home');
    Route::get('/businesses/{business}/workspace', [\App\Http\Controllers\Vendor\BusinessWorkspaceController::class, 'show'])
        ->whereNumber('business')->name('vendor.business.workspace');
    Route::post('/businesses', [VendorWorkspaceController::class, 'storeBusiness'])
        ->name('vendor.businesses.store');
    Route::post('/businesses/{business}/branches', [VendorWorkspaceController::class, 'storeBranch'])
        ->whereNumber('business')
        ->name('vendor.branches.store');
    Route::get('/branches/{branch}/edit', [\App\Http\Controllers\Vendor\BranchChangeRequestController::class, 'edit'])
        ->whereNumber('branch')->name('vendor.branch.edit');
    Route::post('/branches/{branch}/request-change', [\App\Http\Controllers\Vendor\BranchChangeRequestController::class, 'submit'])
        ->whereNumber('branch')->name('vendor.branch.changes.store');
    Route::get('/branches/{branch}/licenses', [BranchLicenseController::class, 'index'])
        ->whereNumber('branch')->name('vendor.branch.licenses.index');
    Route::post('/branches/{branch}/licenses', [BranchLicenseController::class, 'store'])
        ->whereNumber('branch')->name('vendor.branch.licenses.store');
    Route::put('/branches/{branch}/licenses/{license}', [BranchLicenseController::class, 'update'])
        ->whereNumber('branch')->whereNumber('license')->name('vendor.branch.licenses.update');
});

// Private address dropdown for active Vendor users.
Route::middleware(['auth', EnsureVendorAccount::class])
    ->get('/address-options', [\App\Http\Controllers\Vendor\IndiaPostDirectoryController::class, 'options'])
    ->middleware('throttle:60,1')->name('vendor.address.options');

// Private Sublimation Studio routes (kept isolated for this business type).
require __DIR__.'/sublimation.php';

// Outlet finance/stock opening: vendor-owner-only, review-gated.
require __DIR__.'/vendor-opening.php';

// Vendor-owned delivery dispatch (guarded by delivery module switch).
require __DIR__.'/vendor-delivery.php';

// TIDONG additive multi-module integration (no existing routes removed).
require __DIR__.'/tidong_integrated_vendor.php';

Route::middleware(['auth', EnsureVendorAccount::class])->prefix('branches/{branch}/custom-items')
    ->whereNumber('branch')->name('vendor.custom-items.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Vendor\CustomCatalogItemController::class,'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Vendor\CustomCatalogItemController::class,'store'])->name('store');
    });

Route::middleware(['auth', EnsureVendorAccount::class])->prefix('global-catalog')
    ->name('vendor.global-catalog.')->group(function () {
        Route::get('/{type}', [\App\Http\Controllers\Vendor\VendorGlobalMasterController::class,'index'])
          ->where('type','brands|item_categories|units|variant_attributes|variant_values')->name('index');
        Route::post('/{type}', [\App\Http\Controllers\Vendor\VendorGlobalMasterController::class,'store'])
          ->where('type','brands|item_categories|units|variant_attributes|variant_values')->name('store');
    });
