<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Vendor\VendorController;
use App\Http\Controllers\MenuController;

Route::get('/dashboard', [VendorController::class, 'dashboard'])->name('dashboard');

Route::get('/categories', [VendorController::class, 'categoriesPage'])->name('categories.index');
Route::get('/inventory', [VendorController::class, 'inventoryPage'])->name('inventory.index');

Route::get('/pricing', [VendorController::class, 'pricingPage'])->name('pricing.index');
Route::put('/pricing/update/{id}', [VendorController::class, 'updatePricing'])->name('pricing.update');
Route::get('/menus', [MenuController::class, 'index'])->name('menus.index');

Route::get('/catalog', [VendorController::class, 'index'])->name('catalog');
Route::get('/items', [VendorController::class, 'inventoryPage'])->name('items');

Route::post('/categories/save', [VendorController::class, 'saveCategories'])->name('categories.save');
Route::post('/inventory/add', [VendorController::class, 'addItemsToInventory'])->name('inventory.add');
Route::post('/items/add', [VendorController::class, 'addItemsToInventory'])->name('items.add'); 

Route::put('/inventory/update/{id}', [VendorController::class, 'updateInventoryItem'])->name('inventory.update');
Route::post('/item/request', [VendorController::class, 'requestNewItem'])->name('item.request');
Route::get('/qr-code', [VendorController::class, 'showQrCode'])->name('qrcode');