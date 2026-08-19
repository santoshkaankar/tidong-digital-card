<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\UpdateController;
use App\Http\Controllers\Member\CardController;
use App\Http\Controllers\Vendor\MenuController;
use App\Http\Controllers\Admin\MemberManagementController;
use App\Http\Controllers\Admin\VendorManagementController;
use App\Http\Controllers\Admin\EmployeeManagementController;

// Admin Panel Group with Prefix and Name Mapping
Route::middleware(['web', 'auth'])->prefix('admin')->name('admin.')->group(function () {

    // ==========================================
    // 1. ADMIN DASHBOARD & CORE MANAGEMENT
    // ==========================================
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    Route::delete('/delete/{id}', [AdminController::class, 'destroy'])->name('delete');
    Route::post('/register-user', [AdminController::class, 'storeUser'])->name('store.user');

    // Members Management Routes
    Route::get('/members/manage', [MemberManagementController::class, 'index'])->name('members.manage');
    Route::post('/members/save', [MemberManagementController::class, 'saveOrUpdate'])->name('members.save');
    Route::put('/members/{id}/update', [MemberManagementController::class, 'updateDetails'])->name('members.update.details');

    Route::get('/members/search_member', [MemberManagementController::class, 'searchmember'])->name('members.search_member');

    // Vendors Management Routes
    Route::get('/vendors/manage', [VendorManagementController::class, 'index'])->name('vendors.manage');
    Route::post('/vendors/save', [VendorManagementController::class, 'saveOrUpdate'])->name('vendors.save');

    // Employees Management Routes
    Route::get('/employees/manage', [EmployeeManagementController::class, 'index'])->name('employees.manage');
    Route::post('/employees/save', [EmployeeManagementController::class, 'saveOrUpdate'])->name('employees.save');

    // ==========================================
    // 2. APPROVALS & MODERATION (Items, Vendors)
    // ==========================================
    Route::get('/pending-items', [AdminController::class, 'pendingItems'])->name('pending.items');
    Route::post('/item/approve/{id}', [AdminController::class, 'approveItem'])->name('item.approve');
    Route::delete('/item/reject/{id}', [AdminController::class, 'rejectItem'])->name('item.reject');

    // ==========================================
    // 3. MASTERS (Global Items & Categories)
    // ==========================================
    Route::get('/global-item/create', [AdminController::class, 'createGlobalItem'])->name('global.item.create');
    Route::post('/global-item', [AdminController::class, 'storeGlobalItem'])->name('global.item.store');
    Route::post('/item-category/store', [AdminController::class, 'storeItemCategory'])->name('item.category.store');

    // ==========================================
    // 4. VENDOR MANAGEMENT & CATEGORIES
    // ==========================================
    Route::get('/vendor-categories', [AdminController::class, 'createVendorCategory'])->name('vendor.categories');
    Route::post('/vendor-categories', [AdminController::class, 'storeVendorCategory'])->name('vendor.categories.store');
    Route::delete('/vendor-categories/{id}', [AdminController::class, 'destroyVendorCategory'])->name('vendor.categories.delete');

    // ==========================================
    // 5. MEMBER / VISITING CARDS MANAGEMENT
    // ==========================================
    Route::get('/cards', [CardController::class, 'index'])->name('cards.index');
    Route::get('/create-card', [CardController::class, 'create'])->name('card.create');
    Route::post('/store-card', [CardController::class, 'store'])->name('card.store');
    Route::get('/card/{id}/edit', [CardController::class, 'edit'])->name('card.edit');
    Route::post('/card/{id}/update', [CardController::class, 'update'])->name('card.update');

    Route::get('/cards/manage', [AdminController::class, 'manageCardSearch'])->name('cards.manage');
    Route::post('/cards/save-or-update', [AdminController::class, 'saveOrUpdateCard'])->name('cards.saveOrUpdate');

    // Members Management Routes
Route::get('/members/manage', [MemberManagementController::class, 'index'])->name('members.manage');
Route::post('/members/save', [MemberManagementController::class, 'saveOrUpdate'])->name('members.save');
Route::put('/members/{id}/update', [MemberManagementController::class, 'updateDetails'])->name('members.update.details');

// ADD THIS: Dedicated Admin Location Search Route
Route::get('/search-locations', [MemberManagementController::class, 'searchLocations'])->name('search.locations');

    // ==========================================
    // 6. MENU & QR SETUP
    // ==========================================
    Route::get('/menu/create', [MenuController::class, 'createMenu'])->name('menu.create');
    Route::post('/menu/store', [MenuController::class, 'storeMenu'])->name('menu.store');
    Route::post('/menu/item/store', [MenuController::class, 'storeItem'])->name('menu.item.store');

    // ==========================================
    // 7. SYSTEM & UPDATES
    // ==========================================
    Route::get('/update', [UpdateController::class, 'showUploadForm'])->name('update');
    Route::post('/update', [UpdateController::class, 'uploadUpdate'])->name('upload_update');

});

