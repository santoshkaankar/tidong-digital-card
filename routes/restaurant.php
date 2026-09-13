<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Restaurant\DashboardController;
use App\Http\Controllers\Restaurant\CategoryController;
use App\Http\Controllers\Restaurant\ItemController;
use App\Http\Controllers\Restaurant\TableController;
use App\Http\Controllers\Restaurant\OrderController;
use App\Http\Controllers\Restaurant\MenuCardController;
use App\Http\Controllers\Customer\Restaurant\CustomerRestaurantController;
use App\Http\Controllers\Restaurant\KitchenDisplayController;
use App\Http\Controllers\Restaurant\WaiterCallController;
use App\Http\Controllers\Restaurant\KitchenOrderController;
use App\Http\Controllers\Restaurant\CompletedOrderController;
use App\Http\Controllers\Restaurant\OrderCashController;

/*
|--------------------------------------------------------------------------
| Vendor Panel Routes (Dashboard, Menu, POS, Orders)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:vendor'])->prefix('vendor/restaurant')->name('vendor.restaurant.')->group(function () {
    // Dashboard Only
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Menu Categories
    Route::resource('categories', CategoryController::class);

    // Food Items
    Route::post('/items/select-global', [ItemController::class, 'selectGlobalItem'])->name('items.select-global');
    Route::post('/items/store-custom', [ItemController::class, 'storeCustomItem'])->name('items.store-custom');
    Route::resource('items', ItemController::class);
    Route::post('/items/{id}/toggle-status', [ItemController::class, 'toggleStatus'])->name('items.toggle-status');

    // Menu Card / Catalog
    Route::get('/menu-card', [MenuCardController::class, 'index'])->name('menu-card.index');
    Route::post('/menu-card', [MenuCardController::class, 'store'])->name('menu-card.store');
    Route::put('/menu-card/{id}', [MenuCardController::class, 'update'])->name('menu-card.update');
    Route::delete('/menu-card/{id}', [MenuCardController::class, 'destroy'])->name('menu-card.destroy');
    Route::post('/menu-card/{id}/copy', [MenuCardController::class, 'copy'])->name('menu-card.copy');

    // Dining Tables & QR Codes
    Route::resource('tables', TableController::class);

    // POS Billing & Order Management
    Route::get('/pos', [OrderController::class, 'posIndex'])->name('pos.index');
    Route::post('/pos/place-order', [OrderController::class, 'storePosOrder'])->name('pos.place-order');
    Route::post('/pos/store', [OrderController::class, 'storePosOrder'])->name('pos.store');

    // Orders History & Edit Routes
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{id}/edit', [OrderController::class, 'edit'])->name('orders.edit');
    Route::put('/orders/{id}', [OrderController::class, 'update'])->name('orders.update');
    Route::get('/orders/{id}/print', [OrderController::class, 'printReceipt'])->name('orders.print');
});

// Alias Route mapping for compatibility with vendor.pos.store JS fetch request
Route::middleware(['auth', 'role:vendor'])->post('/vendor/restaurant/pos/order', [OrderController::class, 'storePosOrder'])->name('vendor.pos.store');

/*
|--------------------------------------------------------------------------
| Public Customer QR Scan & Live Order Routes
|--------------------------------------------------------------------------
*/
Route::prefix('menu')->name('customer.restaurant.')->group(function () {
    Route::get('/table/{token}', [CustomerRestaurantController::class, 'showMenu'])->name('menu');
    Route::post('/table/{token}/order', [CustomerRestaurantController::class, 'placeOrder'])->name('order.place');
    Route::get('/table/{token}/status/{order_id}', [CustomerRestaurantController::class, 'getOrderStatus'])->name('order.status');
    Route::post('/table/{token}/call-waiter', [CustomerRestaurantController::class, 'callWaiter'])->name('call_waiter');
    Route::post('/table/{token}/request-payment', [CustomerRestaurantController::class, 'requestPayment'])->name('payment.request');
});

/*
|--------------------------------------------------------------------------
| KDS & Kitchen Management Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:vendor'])->prefix('vendor/restaurant')->name('vendor.restaurant.')->group(function () {
    // KDS Screen Main Page
    Route::get('/kitchen-screen', [KitchenDisplayController::class, 'index'])->name('kitchen.screen');

    // Order Details Popup Endpoint
    Route::get('/kitchen-orders/{id}/details', [KitchenDisplayController::class, 'getOrderDetails'])->name('kitchen.order_details');

    // Polling & Operations
    Route::get('/kitchen-screen/live-orders', [KitchenDisplayController::class, 'liveOrders'])->name('kitchen.live_orders');
    Route::post('/kitchen-screen/mark-paid/{id}', [KitchenDisplayController::class, 'markPaymentReceived'])->name('kitchen.mark_paid');

    // Waiter Calls
    Route::post('/waiter-calls/{id}/resolve', [WaiterCallController::class, 'resolve'])->name('waiter_call.resolve');

    // Order Status Update
    Route::post('/kitchen-orders/{id}/status', [KitchenDisplayController::class, 'updateOrderStatus'])->name('kitchen_order.update_status');

    // Completed Orders Log Route
    Route::get('/completed-orders', [CompletedOrderController::class, 'index'])->name('completed_orders.index');
});

// Dedicated Route for Cash Requests
Route::middleware(['auth'])->prefix('vendor/restaurant')->name('vendor.restaurant.')->group(function () {
    Route::post('/cash-call/resolve/{id}', [OrderCashController::class, 'resolve'])->name('cash_call.resolve');
});

Route::get('/vendor/restaurant/orders/{id}/receipt', [App\Http\Controllers\Restaurant\OrderController::class, 'printReceipt'])
    ->name('vendor.restaurant.orders.receipt');