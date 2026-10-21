<?php

use App\Http\Controllers\Affiliate\AffiliateAdminController;
use App\Http\Controllers\Affiliate\AffiliateController;
use App\Http\Middleware\EnsureCustomerAccount;
use App\Http\Middleware\EnsureSuperAdmin;
use Illuminate\Support\Facades\Route;

// Affiliate endpoints are disabled until an authorized admin enables this module.
Route::middleware(\App\Http\Middleware\EnsureAffiliateModuleEnabled::class)->group(function (): void {
Route::get('/affiliate/ref/{code}/{leg}', [AffiliateController::class, 'referral'])
    ->where('leg', '[aAbB]')->middleware('throttle:20,1')->name('affiliate.referral');

Route::middleware(['auth', EnsureCustomerAccount::class])->group(function (): void {
    Route::get('/affiliate', [AffiliateController::class, 'index'])->name('affiliate.index');
    Route::post('/affiliate/enroll', [AffiliateController::class, 'enroll'])
        ->middleware('throttle:5,1')->name('affiliate.enroll');
});

Route::middleware(['auth', EnsureSuperAdmin::class])->group(function (): void {
    Route::get('/admin/affiliate', [AffiliateAdminController::class, 'index'])->name('affiliate.admin.index');
    Route::post('/admin/affiliate/root', [AffiliateAdminController::class, 'initializeRoot'])
        ->middleware('throttle:3,1')->name('affiliate.admin.root');
    Route::post('/admin/affiliate/settings', [AffiliateAdminController::class, 'update'])
        ->middleware('throttle:10,1')->name('affiliate.admin.settings');
    Route::post('/admin/affiliate/stage/{stage}', [AffiliateAdminController::class, 'updateStage'])
        ->whereNumber('stage')->middleware('throttle:10,1')->name('affiliate.admin.stage');
});

});
