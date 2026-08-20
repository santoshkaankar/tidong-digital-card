<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Member\CardController;
use App\Http\Controllers\Member\WalletController;
use App\Http\Controllers\Member\FriendController;
use App\Http\Controllers\Member\SearchController;
use App\Http\Controllers\Member\ProfileController;

// Dashboard
Route::get('/dashboard', function () {
    return view('member.dashboard');
})->name('dashboard');

// Profile Routes
Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

// Card Indexing & Dashboards (All Name Aliases Included)
Route::get('/cards', [CardController::class, 'index'])->name('cards.index');
Route::get('/cards-list', [CardController::class, 'index'])->name('card.index');

// Master Profile Form Routes
Route::get('/card/configure', [CardController::class, 'configure'])->name('card.configure');
Route::post('/card/configure', [CardController::class, 'storeMaster'])->name('card.configure.store');

// Location Search Route (Select2)
Route::get('/search-locations', [CardController::class, 'searchLocations'])->name('search.locations');

// Direct CRUD Operations for Visiting Cards
Route::get('/card/create', [CardController::class, 'create'])->name('card.create');
Route::get('/card/create-view', [CardController::class, 'create'])->name('card.view.create');
Route::post('/card/store', [CardController::class, 'store'])->name('card.store');
Route::post('/card/store-view', [CardController::class, 'storeDesign'])->name('card.view.store');
Route::get('/card/{id}/edit', [CardController::class, 'configure'])->name('card.edit');
Route::post('/card/{id}/update', [CardController::class, 'update'])->name('card.update.post');
Route::put('/card/{id}', [CardController::class, 'update'])->name('card.update');
Route::delete('/card/{id}', [CardController::class, 'destroy'])->name('card.destroy');
Route::delete('/card/{id}/destroy', [CardController::class, 'destroy'])->name('card.view.destroy');

// Advanced Search
Route::get('/search', [SearchController::class, 'index'])->name('search');

// Wallet Route
Route::get('/wallet', [WalletController::class, 'index'])->name('wallet');

// Friend Circle Routes
Route::get('/friend-circle/{type}', [FriendController::class, 'index'])->name('friend.index');

// Public Visiting Card Route
Route::get('/card/v/{slug}', [CardController::class, 'showPublic'])->name('card.public');

// Orders View
Route::get('/orders', function () {
    return view('member.orders');
})->name('orders');