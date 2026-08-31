<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\VendorManagementController;
use App\Http\Controllers\Admin\MemberManagementController;
use App\Http\Controllers\Admin\EmployeeManagementController;
use App\Http\Controllers\UpdateController;

Route::middleware(['web', 'auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    // 1. Dashboard & Core
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    Route::delete('/delete/{id}', [AdminController::class, 'destroy'])->name('delete');
    Route::post('/register-user', [AdminController::class, 'storeUser'])->name('store.user');

    // 2. Members Management
    Route::get('/members/manage', [MemberManagementController::class, 'index'])->name('members.manage');
    Route::post('/members/save', [MemberManagementController::class, 'saveOrUpdate'])->name('members.save');
    Route::put('/members/{id}/update', [MemberManagementController::class, 'updateDetails'])->name('members.update.details');
    Route::get('/members/search_member', [MemberManagementController::class, 'searchmember'])->name('members.search_member');

    // 3. Vendors Management
    Route::get('/vendors/manage', [VendorManagementController::class, 'index'])->name('vendors.manage');
    Route::post('/vendors/save', [VendorManagementController::class, 'saveOrUpdate'])->name('vendors.save');
    Route::get('/vendor-categories', [VendorManagementController::class, 'createVendorCategory'])->name('vendor.categories');
    Route::post('/vendor-categories', [VendorManagementController::class, 'storeVendorCategory'])->name('vendor.categories.store');
    Route::delete('/vendor-categories/{id}', [VendorManagementController::class, 'destroyVendorCategory'])->name('vendor.categories.delete');

    // 4. Global Master Items
    Route::get('/global-items', [VendorManagementController::class, 'indexGlobalItem'])->name('global.items.index');
    Route::get('/global-item/create', [VendorManagementController::class, 'createGlobalItem'])->name('global.item.create');
    Route::post('/global-item', [VendorManagementController::class, 'storeGlobalItem'])->name('global.item.store');
    Route::get('/global-item/edit/{id}', [VendorManagementController::class, 'editGlobalItem'])->name('global.item.edit');
    Route::put('/global-item/update/{id}', [VendorManagementController::class, 'updateGlobalItem'])->name('global.item.update');
    Route::delete('/global-item/delete/{id}', [VendorManagementController::class, 'destroyGlobalItem'])->name('global.item.delete');
    Route::post('/item-category/store', [VendorManagementController::class, 'storeItemCategory'])->name('item.category.store');

    // Item Moderation / Approvals (Vendor Request Approval System)
    Route::get('/pending-items', [AdminController::class, 'pendingGlobalItems'])->name('pending.items');
    Route::post('/item/approve/{id}', [AdminController::class, 'approveGlobalItem'])->name('item.approve');
    Route::delete('/item/reject/{id}', [AdminController::class, 'rejectGlobalItem'])->name('item.reject');

    // 5. Employees Management
    Route::get('/employees/manage', [EmployeeManagementController::class, 'index'])->name('employees.manage');
    Route::post('/employees/save', [EmployeeManagementController::class, 'saveOrUpdate'])->name('employees.save');

    // 6. Member Cards Admin Control
    Route::get('/cards/manage', [AdminController::class, 'manageCardSearch'])->name('cards.manage');
    Route::post('/cards/save-or-update', [AdminController::class, 'saveOrUpdateCard'])->name('cards.saveOrUpdate');

    // 7. System & Updates
    Route::get('/update', [UpdateController::class, 'showUploadForm'])->name('update');
    Route::post('/update', [UpdateController::class, 'uploadUpdate'])->name('upload_update');
});