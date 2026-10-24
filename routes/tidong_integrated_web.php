<?php
// Added by TIDONG multi-module source installer; loaded inside existing web routes.
\Illuminate\Support\Facades\Route::middleware(\App\Http\Middleware\EnsureTidongModuleEnabled::class.':grocery')->group(function (): void { require __DIR__.'/grocery_shop.php'; });
require __DIR__.'/affiliate.php';

// Legal professional private routes: never grant platform employees access to vendor data.
\Illuminate\Support\Facades\Route::middleware(['auth', \App\Http\Middleware\EnsureTidongModuleEnabled::class.':legal_advisory'])->prefix('professional/legal-advisory')->name('legal.professional.')->group(function () {
    \Illuminate\Support\Facades\Route::get('/', [\App\Http\Controllers\LegalAdvisory\ProfessionalPortalController::class, 'index'])->name('index');
    \Illuminate\Support\Facades\Route::post('/engagements/{engagement}/work-updates', [\App\Http\Controllers\LegalAdvisory\ProfessionalPortalController::class, 'recordWork'])->whereNumber('engagement')->middleware('throttle:12,1')->name('work-updates.store');
    \Illuminate\Support\Facades\Route::post('/engagements/{engagement}/accept', [\App\Http\Controllers\LegalAdvisory\ProfessionalPortalController::class, 'accept'])->whereNumber('engagement')->name('accept');
});
\Illuminate\Support\Facades\Route::middleware(['auth', \App\Http\Middleware\EnsureTidongModuleEnabled::class.':legal_advisory'])->prefix('professional/legal-advisory/application')->name('legal.application.')->group(function () {
    \Illuminate\Support\Facades\Route::get('/', [\App\Http\Controllers\LegalAdvisory\ProfessionalApplicationController::class, 'index'])->name('index');
    \Illuminate\Support\Facades\Route::post('/', [\App\Http\Controllers\LegalAdvisory\ProfessionalApplicationController::class, 'store'])->name('store')->middleware('throttle:5,1');
});
