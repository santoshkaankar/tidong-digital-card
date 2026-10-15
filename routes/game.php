<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Game\GameController;
use App\Http\Controllers\Game\MiningController;
use App\Http\Controllers\Game\FactoryController;
use App\Http\Controllers\Game\MarketplaceController;
use App\Http\Controllers\Game\AdminGameController;

// 1. Members / Customers Game Access
Route::middleware(['web', 'auth'])->prefix('game')->name('game.')->group(function () {
    
    // Main Game Screen (Three.js Partials Layout)
    Route::get('/', [GameController::class, 'index'])->name('index');

    // Action APIs
    Route::post('/mine', [MiningController::class, 'mineResource'])->name('mine');
    Route::post('/factory/produce', [FactoryController::class, 'produceItem'])->name('factory.produce');
    Route::post('/market/buy', [MarketplaceController::class, 'buyItem'])->name('market.buy');
    Route::post('/market/resale', [MarketplaceController::class, 'listResale'])->name('market.resale');

    // 2. Admin & Employee Management Dashboard (Papa, Gaurang & Saharsh)
    Route::middleware(['role:admin,employee'])->prefix('manage')->name('manage.')->group(function () {
        Route::get('/dashboard', [AdminGameController::class, 'index'])->name('dashboard');
        Route::post('/update-economy', [AdminGameController::class, 'updateEconomy'])->name('update.economy');
    });
});