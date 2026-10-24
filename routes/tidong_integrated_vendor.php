<?php
\Illuminate\Support\Facades\Route::middleware(\App\Http\Middleware\EnsureTidongModuleEnabled::class.':grocery,vendor')->group(function (): void { require __DIR__.'/grocery_vendor.php'; });
\Illuminate\Support\Facades\Route::middleware(['auth', \App\Http\Middleware\EnsureVendorAccount::class, \App\Http\Middleware\EnsureTidongModuleEnabled::class.':legal_advisory,vendor'])->prefix('legal-advisory')->name('vendor.legal.')->group(function () {
    \Illuminate\Support\Facades\Route::get('/', [\App\Http\Controllers\Vendor\LegalAdvisoryController::class, 'index'])->name('index');
    \Illuminate\Support\Facades\Route::get('/engagements/{engagement}/progress', [\App\Http\Controllers\Vendor\LegalAdvisoryController::class, 'progressPage'])->whereNumber('engagement')->name('progress');
    \Illuminate\Support\Facades\Route::get('/engagements/{engagement}/ai-progress', [\App\Http\Controllers\Vendor\LegalAdvisoryController::class, 'aiProgress'])->whereNumber('engagement')->middleware('throttle:20,1')->name('ai-progress');
    \Illuminate\Support\Facades\Route::post('/invite', [\App\Http\Controllers\Vendor\LegalAdvisoryController::class, 'invite'])->middleware('throttle:12,1')->name('invite');
    \Illuminate\Support\Facades\Route::post('/engagements/{engagement}/end', [\App\Http\Controllers\Vendor\LegalAdvisoryController::class, 'end'])->whereNumber('engagement')->name('end');
});
