<?php
require __DIR__.'/grocery_admin.php';
\Illuminate\Support\Facades\Route::middleware(['auth', \App\Http\Middleware\EnsureSuperAdmin::class])->prefix('legal-features')->name('admin.legal.features.')->group(function () {
    \Illuminate\Support\Facades\Route::get('/', [\App\Http\Controllers\Admin\LegalFeaturesController::class, 'index'])->name('index');
    \Illuminate\Support\Facades\Route::post('/', [\App\Http\Controllers\Admin\LegalFeaturesController::class, 'update'])->name('update');
});
\Illuminate\Support\Facades\Route::middleware(['auth', \App\Http\Middleware\EnsureSuperAdmin::class])->prefix('legal-qualifications')->name('admin.legal.qualifications.')->group(function () {
    \Illuminate\Support\Facades\Route::get('/', [\App\Http\Controllers\Admin\LegalQualificationReviewController::class, 'index'])->name('index');
    \Illuminate\Support\Facades\Route::post('/{qualification}/decision', [\App\Http\Controllers\Admin\LegalQualificationReviewController::class, 'decide'])->whereNumber('qualification')->name('decide');
});
