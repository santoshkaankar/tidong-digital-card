<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Member\CardController;
use App\Http\Controllers\Member\WalletController;
use App\Http\Controllers\Member\FriendController;
use App\Http\Controllers\Member\SearchController;
use App\Http\Controllers\Member\ProfileController;

Route::middleware(['web', 'auth', 'role:user,member'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', function () {
        return view('member.dashboard');
    })->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

    // Visiting Cards Management
    Route::get('/cards', [CardController::class, 'index'])->name('cards.index');
    Route::get('/cards-list', [CardController::class, 'index'])->name('card.index');
    Route::get('/card/configure', [CardController::class, 'configure'])->name('card.configure');
    Route::post('/card/configure', [CardController::class, 'storeMaster'])->name('card.configure.store');

    // CRUD & View Alias Routes (Fix for cards-index.blade.php)
    Route::get('/card/create', [CardController::class, 'create'])->name('card.create');
    Route::get('/card/create-design', [CardController::class, 'create'])->name('card.create_design');
    Route::get('/card/create-view', [CardController::class, 'create'])->name('card.view.create');
    Route::post('/card/store', [CardController::class, 'store'])->name('card.store');
    Route::post('/card/store-view', [CardController::class, 'storeDesign'])->name('card.view.store');
    Route::get('/card/{id}/edit', [CardController::class, 'configure'])->name('card.edit');
    Route::post('/card/{id}/update', [CardController::class, 'update'])->name('card.update.post');
    Route::put('/card/{id}', [CardController::class, 'update'])->name('card.update');
    Route::delete('/card/{id}', [CardController::class, 'destroy'])->name('card.destroy');
    Route::delete('/card/{id}/destroy', [CardController::class, 'destroy'])->name('card.view.destroy');

    // Public Card Link inside Member Prefix (Fixes member.card.public error)
    Route::get('/card/v/{slug}', [CardController::class, 'showPublic'])->name('card.public');

    // Search, Wallet & Friends
    Route::get('/search', [SearchController::class, 'index'])->name('search');
    Route::get('/wallet', [WalletController::class, 'index'])->name('wallet');
    Route::get('/friend-circle/{type}', [FriendController::class, 'index'])->name('friend.index');
    Route::get('/orders', function () {
        return view('member.orders');
    })->name('orders');
});