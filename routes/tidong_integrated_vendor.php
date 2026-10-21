<?php
\Illuminate\Support\Facades\Route::middleware(\App\Http\Middleware\EnsureTidongModuleEnabled::class.':grocery')->group(function (): void { require __DIR__.'/grocery_vendor.php'; });
\Illuminate\Support\Facades\Route::middleware(['auth', \App\Http\Middleware\EnsureVendorAccount::class, \App\Http\Middleware\EnsureTidongModuleEnabled::class.':legal_advisory'])->prefix('legal-advisory')->name('vendor.legal.')->group(function () {
    \Illuminate\Support\Facades\Route::get('/', [\App\Http\Controllers\Vendor\LegalAdvisoryController::class, 'index'])->name('index');
    \Illuminate\Support\Facades\Route::post('/invite', [\App\Http\Controllers\Vendor\LegalAdvisoryController::class, 'invite'])->middleware('throttle:12,1')->name('invite');
    \Illuminate\Support\Facades\Route::post('/engagements/{engagement}/end', [\App\Http\Controllers\Vendor\LegalAdvisoryController::class, 'end'])->whereNumber('engagement')->name('end');
});
