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
use App\Http\Controllers\Restaurant\CustomItemController;
use App\Http\Controllers\Restaurant\TiffinCatalogController;
use App\Http\Controllers\Restaurant\ProceedTiffinController;


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

    /* -------------------------------------------------
     * Food Items & Custom Items (Static routes sabse upar)
     * ------------------------------------------------- */
    Route::get('/items/create-custom', [CustomItemController::class, 'create'])->name('items.create_custom');
    Route::post('/items/store-custom', [CustomItemController::class, 'store'])->name('items.storecustom');
    Route::delete('/items/custom/{id}', [CustomItemController::class, 'destroy'])->name('custom-items.destroy');
    
    Route::post('/items/select-global', [ItemController::class, 'selectGlobalItem'])->name('items.select-global');
    Route::post('/items/{id}/toggle-status', [ItemController::class, 'toggleStatus'])->name('items.toggle-status');
    
    // Resource route ab niche rahega taaki upar ke static URLs clash na karein
    Route::resource('items', ItemController::class);

    // Menu Card / Catalog
    Route::get('/menu-card', [MenuCardController::class, 'index'])->name('menu-card.index');
    Route::post('/menu-card', [MenuCardController::class, 'store'])->name('menu-card.store');
    Route::put('/menu-card/{id}', [MenuCardController::class, 'update'])->name('menu-card.update');
    Route::delete('/menu-card/{id}', [MenuCardController::class, 'destroy'])->name('menu-card.destroy');
    Route::post('/menu-card/{id}/copy', [MenuCardController::class, 'copy'])->name('menu-card.copy');

    // Tiffin Menu Routes
    Route::get('/weekly-menu', [TiffinCatalogController::class, 'index'])->name('weekly-menu.index');
    Route::post('/weekly-menu', [TiffinCatalogController::class, 'store'])->name('weekly-menu.store');
    Route::post('/weekly-menu/store', [TiffinCatalogController::class, 'store']); // <-- Yeh line extra add kar lo taaki 404 na aaye
    Route::get('/weekly-menu/{id}', [TiffinCatalogController::class, 'show'])->name('weekly-menu.show');
    Route::get('/weekly-menu/{id}/edit', [TiffinCatalogController::class, 'edit'])->name('weekly-menu.edit');
    Route::put('/weekly-menu/{id}', [TiffinCatalogController::class, 'update'])->name('weekly-menu.update');
    Route::delete('/weekly-menu/{id}', [TiffinCatalogController::class, 'destroy'])->name('weekly-menu.destroy');

    // Proceed Tiffin Routes (Static routes pehle, dynamic route {id} sabse baad mein)
    Route::get('proceed-tiffin', [ProceedTiffinController::class, 'index'])->name('proceed-tiffin.index');
    Route::get('proceed-tiffin/list', [ProceedTiffinController::class, 'listSaved'])->name('proceed-tiffin.list');
    Route::get('proceed-tiffin/schedule', [ProceedTiffinController::class, 'scheduleView'])->name('proceed-tiffin.schedule');
    Route::post('/proceed-tiffin', [ProceedTiffinController::class, 'store'])->name('proceed-tiffin.store');
    Route::get('/proceed-tiffin/{id}', [ProceedTiffinController::class, 'show'])->name('proceed-tiffin.show');
    
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
    Route::get('/orders/{id}/receipt', [OrderController::class, 'printReceipt'])->name('orders.receipt');
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

    // Cash Requests
    Route::post('/cash-call/resolve/{id}', [OrderCashController::class, 'resolve'])->name('cash_call.resolve');
});