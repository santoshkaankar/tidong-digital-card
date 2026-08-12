<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Http\Controllers\VisitingCardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\UpdateController;

// ==========================================
// MAKE REGISTERED USER ADMIN ROUTE
// ==========================================
Route::get('/check-database', function () {
    // Check if admin already exists, if not create one automatically
    $admin = User::firstOrCreate(
        ['email' => 'admin@tidong.in'],
        [
            'name' => 'Santosh Kumar Sharma',
            'password' => Hash::make('password123'),
            'role' => 'admin'
        ]
    );

    $users = User::all();
    return response()->json([
        'message' => 'Default admin ensured / Database users list:',
        'users' => $users
    ]);
});

Route::get('/run-pincode-seeder', function () {
    try {
        $seeder = new \Database\Seeders\PincodeSeeder();
        // Console output capture karne ke liye fake command object
        $seeder->run();
        return "<h1>Success! Saare Pincodes database mein seed ho gaye hain.</h1>";
    } catch (\Exception $e) {
        return "<h1>Error:</h1> " . $e->getMessage();
    }
});

// ==========================================
// TEMPORARY STABLE ROUTE FOR LIVE RUNNING
// ==========================================
Route::get('/setup-live-database', function () {
    return "<h1>Website is Live & Running! Aap aram se editing kijiye.</h1><p><a href='/login'>Go to Login</a></p>";
});

// 1. Welcome Page
Route::get('/', function () {
    return view('welcome');
});

// 2. Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// 3. Public Menu & QR / Card Routes (No Login Required)
Route::get('/card/{id}', [VisitingCardController::class, 'show'])->name('card.show');
Route::get('/search-locations', [VisitingCardController::class, 'searchLocations']);
Route::get('/menu/{slug}', [MenuController::class, 'showPublicMenu'])->name('menu.public');
Route::post('/menu/{slug}/order', [MenuController::class, 'placeOrder'])->name('menu.order');
Route::post('/order/{orderId}/complete', [MenuController::class, 'completeOrder'])->name('order.complete');


// ==========================================
// 4. AUTHENTICATED & ROLE-BASED ROUTES
// ==========================================
Route::middleware(['auth'])->group(function () {

    // --- ADMIN ROUTES ---
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
        Route::delete('/delete/{id}', [AdminController::class, 'destroy'])->name('delete');
        Route::post('/register-user', [AdminController::class, 'storeUser'])->name('store.user');

        // Pending Items
        Route::get('/pending-items', [AdminController::class, 'pendingItems'])->name('pending.items');
        Route::post('/item/approve/{id}', [AdminController::class, 'approveItem'])->name('item.approve');
        Route::delete('/item/reject/{id}', [AdminController::class, 'rejectItem'])->name('item.reject');
        
        // Global Master & Item Categories Routes
        Route::get('/global-item/create', [AdminController::class, 'createGlobalItem'])->name('global.item.create');
        Route::post('/global-item', [AdminController::class, 'storeGlobalItem'])->name('global.item.store');
        Route::post('/item-category/store', [AdminController::class, 'storeItemCategory'])->name('item.category.store');

        // Vendor Categories Routes
        Route::get('/vendor-categories', [AdminController::class, 'createVendorCategory'])->name('vendor.categories');
        Route::post('/vendor-categories', [AdminController::class, 'storeVendorCategory'])->name('vendor.categories.store');
        Route::delete('/vendor-categories/{id}', [AdminController::class, 'destroyVendorCategory'])->name('vendor.categories.delete');

        // Website Updates
        Route::get('/update', [UpdateController::class, 'showUploadForm'])->name('update');
        Route::post('/update', [UpdateController::class, 'uploadUpdate'])->name('upload_update');

        // Card Management
        Route::get('/create-card', [VisitingCardController::class, 'create'])->name('card.create');
        Route::post('/store-card', [VisitingCardController::class, 'store'])->name('card.store');
        Route::get('/card/{id}/edit', [VisitingCardController::class, 'edit'])->name('card.edit');
        Route::post('/card/{id}/update', [VisitingCardController::class, 'update'])->name('card.update');

        // Menu Management
        Route::get('/menu/create', [MenuController::class, 'createMenu'])->name('menu.create');
        Route::post('/menu/store', [MenuController::class, 'storeMenu'])->name('menu.store');
        Route::post('/menu/item/store', [MenuController::class, 'storeItem'])->name('menu.item.store');
    });

    // Global Aliases for Compatibility with Views
    Route::get('/admin/create-card', [VisitingCardController::class, 'create'])->name('card.create');
    Route::post('/admin/store-card', [VisitingCardController::class, 'store'])->name('card.store');
    Route::get('/admin/menu/create', [MenuController::class, 'createMenu'])->name('menu.create');
    Route::post('/admin/menu/store', [MenuController::class, 'storeMenu'])->name('menu.store');
    Route::post('/admin/menu/item/store', [MenuController::class, 'storeItem'])->name('menu.item.store');


    // --- VENDOR DASHBOARD & CATALOG ROUTES ---
    Route::prefix('vendor')->name('vendor.')->group(function () {
        Route::get('/dashboard', [VendorController::class, 'dashboard'])->name('dashboard');
        
        // Separate Pages for Categories and Inventory
        Route::get('/categories', [VendorController::class, 'categoriesPage'])->name('categories.index');
        Route::get('/inventory', [VendorController::class, 'inventoryPage'])->name('inventory.index');
        
        // Pricing Master & Menu Cards (Catalog) Routes
        Route::get('/pricing', [VendorController::class, 'pricingPage'])->name('pricing.index');
        Route::put('/pricing/update/{id}', [VendorController::class, 'updatePricing'])->name('pricing.update');
        Route::get('/menus', [MenuController::class, 'index'])->name('menus.index');

        // Legacy Catalog Route (optional fallback)
        Route::get('/catalog', [VendorController::class, 'index'])->name('catalog');

        Route::get('/items', [VendorController::class, 'inventoryPage'])->name('items');

        Route::post('/categories/save', [VendorController::class, 'saveCategories'])->name('categories.save');
        Route::post('/inventory/add', [VendorController::class, 'addItemsToInventory'])->name('inventory.add');
        Route::post('/items/add', [VendorController::class, 'addItemsToInventory'])->name('items.add'); 

        Route::put('/inventory/update/{id}', [VendorController::class, 'updateInventoryItem'])->name('inventory.update');
        Route::post('/item/request', [VendorController::class, 'requestNewItem'])->name('item.request');
        Route::get('/qr-code', [VendorController::class, 'showQrCode'])->name('qrcode');
    });


    // --- EMPLOYEE DASHBOARD ---
    Route::get('/employee/dashboard', function () {
        return view('employee.dashboard');
    })->name('employee.dashboard');


    // --- CUSTOMER DASHBOARD ---
    Route::get('/customer/dashboard', function () {
        return view('customer.dashboard');
    })->name('customer.dashboard');

});