<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\VendorManagementController;
use App\Http\Controllers\Admin\MemberManagementController;
use App\Http\Controllers\Admin\EmployeeManagementController;
use App\Http\Controllers\Member\CardController;
use App\Http\Controllers\Vendor\MenuController;
use App\Http\Controllers\UpdateController;


// केवल Admin ही इन पेजों को खोल सकता है
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/global-items', [VendorManagementController::class, 'indexGlobalItem'])->name('global.items.index');
    // आपके बाकी Admin Routes...
});

// Admin Panel Group with Prefix and Name Mapping
Route::middleware(['web', 'auth'])->prefix('admin')->name('admin.')->group(function () {

    // ==========================================
    // 1. ADMIN DASHBOARD & CORE MANAGEMENT
    // ==========================================
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    Route::delete('/delete/{id}', [AdminController::class, 'destroy'])->name('delete');
    Route::post('/register-user', [AdminController::class, 'storeUser'])->name('store.user');

    // ==========================================
    // 2. MEMBERS MANAGEMENT
    // ==========================================
    Route::get('/members/manage', [MemberManagementController::class, 'index'])->name('members.manage');
    Route::post('/members/save', [MemberManagementController::class, 'saveOrUpdate'])->name('members.save');
    Route::put('/members/{id}/update', [MemberManagementController::class, 'updateDetails'])->name('members.update.details');
    Route::get('/members/search_member', [MemberManagementController::class, 'searchmember'])->name('members.search_member');
    Route::get('/search-locations', [MemberManagementController::class, 'searchLocations'])->name('search.locations');

    // ==========================================
    // 3. VENDORS MANAGEMENT
    // ==========================================
    Route::get('/vendors/manage', [VendorManagementController::class, 'index'])->name('vendors.manage');
    Route::post('/vendors/save', [VendorManagementController::class, 'saveOrUpdate'])->name('vendors.save');

    // Vendor Categories
    Route::get('/vendor-categories', [VendorManagementController::class, 'createVendorCategory'])->name('vendor.categories');
    Route::post('/vendor-categories', [VendorManagementController::class, 'storeVendorCategory'])->name('vendor.categories.store');
    Route::delete('/vendor-categories/{id}', [VendorManagementController::class, 'destroyVendorCategory'])->name('vendor.categories.delete');

    // ==========================================
    // 4. GLOBAL ITEMS & MASTERS MANAGEMENT
    // ==========================================
    Route::get('/global-items', [VendorManagementController::class, 'indexGlobalItem'])->name('global.items.index');
    Route::get('/global-item/create', [VendorManagementController::class, 'createGlobalItem'])->name('global.item.create');
    Route::post('/global-item', [VendorManagementController::class, 'storeGlobalItem'])->name('global.item.store');
    Route::delete('/global-item/delete/{id}', [VendorManagementController::class, 'destroyGlobalItem'])->name('global.item.delete');
    Route::post('/item-category/store', [VendorManagementController::class, 'storeItemCategory'])->name('item.category.store');
    Route::get('/global-item/edit/{id}', [VendorManagementController::class, 'editGlobalItem'])->name('global.item.edit');
    Route::put('/global-item/update/{id}', [VendorManagementController::class, 'updateGlobalItem'])->name('global.item.update');

    // Item Moderation / Approvals
    Route::get('/pending-items', [VendorManagementController::class, 'pendingItems'])->name('pending.items');
    Route::post('/item/approve/{id}', [VendorManagementController::class, 'approveItem'])->name('item.approve');
    Route::delete('/item/reject/{id}', [VendorManagementController::class, 'rejectItem'])->name('item.reject');

    // ==========================================
    // 5. EMPLOYEES MANAGEMENT
    // ==========================================
    Route::get('/employees/manage', [EmployeeManagementController::class, 'index'])->name('employees.manage');
    Route::post('/employees/save', [EmployeeManagementController::class, 'saveOrUpdate'])->name('employees.save');

    // ==========================================
    // 6. MEMBER / VISITING CARDS MANAGEMENT
    // ==========================================
    Route::get('/cards', [CardController::class, 'index'])->name('cards.index');
    Route::get('/create-card', [CardController::class, 'create'])->name('card.create');
    Route::post('/store-card', [CardController::class, 'store'])->name('card.store');
    Route::get('/card/{id}/edit', [CardController::class, 'edit'])->name('card.edit');
    Route::post('/card/{id}/update', [CardController::class, 'update'])->name('card.update');

    Route::get('/cards/manage', [AdminController::class, 'manageCardSearch'])->name('cards.manage');
    Route::post('/cards/save-or-update', [AdminController::class, 'saveOrUpdateCard'])->name('cards.saveOrUpdate');

    // ==========================================
    // 7. MENU & QR SETUP
    // ==========================================
    Route::get('/menu/create', [MenuController::class, 'createMenu'])->name('menu.create');
    Route::post('/menu/store', [MenuController::class, 'storeMenu'])->name('menu.store');
    Route::post('/menu/item/store', [MenuController::class, 'storeItem'])->name('menu.item.store');

    // ==========================================
    // 8. SYSTEM & UPDATES
    // ==========================================
    Route::get('/update', [UpdateController::class, 'showUploadForm'])->name('update');
    Route::post('/update', [UpdateController::class, 'uploadUpdate'])->name('upload_update');

});
