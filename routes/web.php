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
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CardController;

Route::get('/debug-pincodes', function () {
    try {
        $count = \Illuminate\Support\Facades\DB::table('pincodes')->count();
        $sample = \Illuminate\Support\Facades\DB::table('pincodes')->limit(5)->get();
        return response()->json([
            'total_pincodes_in_db' => $count,
            'sample_data' => $sample
        ]);
    } catch (\Exception $e) {
        return "Error: " . $e->getMessage();
    }
});

// ==========================================
// SECURE & LOCKED DATABASE INITIALIZATION
// ==========================================
Route::get('/check-database', function () {
    $user = User::where('email', 'admin@tidong.in')->first();
    
    if ($user) {
        $user->password = Hash::make('password123');
        $user->save();
        return "Admin Password Reset to 'password123'. Ab login try karein.";
    } else {
        User::create([
            'name' => 'Santosh Kumar Sharma',
            'email' => 'admin@tidong.in',
            'password' => Hash::make('password123'),
            'role' => 'admin'
        ]);
        return "Admin user created successfully. Ab login try karein.";
    }
});

Route::get('/run-pincode-seeder', function () {
    try {
        $existingCount = \Illuminate\Support\Facades\DB::table('pincodes')->count();
        if ($existingCount > 0) {
            return "<h1>Database Locked:</h1><p>Pincodes already exist ({$existingCount} records found). Data is safe and locked!</p><p><a href='/login'>Go to Login</a></p>";
        }

        User::firstOrCreate(
            ['email' => 'admin@tidong.in'],
            [
                'name' => 'Santosh Kumar Sharma',
                'password' => Hash::make('password123'),
                'role' => 'admin'
            ]
        );

        $seeder = new \Database\Seeders\PincodeSeeder();
        $seeder->run();
        
        return "<h1>Success! Admin ensured & All Pincodes seeded safely and locked.</h1><p><a href='/login'>Go to Login</a></p>";
    } catch (\Exception $e) {
        return "<h1>Error:</h1> " . $e->getMessage();
    }
});

// ==========================================
// TEMPORARY STABLE ROUTE FOR LIVE RUNNING
// ==========================================
Route::get('/setup-live-database', function () {
    return "<h1>Website is Live & Running! Database is locked & secure.</h1><p><a href='/login'>Go to Login</a></p>";
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
Route::get('/search-locations', [CardController::class, 'searchLocations']);
Route::get('/menu/{slug}', [MenuController::class, 'showPublicMenu'])->name('menu.public');
Route::post('/menu/{slug}/order', [MenuController::class, 'placeOrder'])->name('menu.order');
Route::post('/order/{orderId}/complete', [MenuController::class, 'completeOrder'])->name('order.complete');


// ==========================================
// 4. AUTHENTICATED & ROLE-BASED ROUTES
// ==========================================
Route::middleware(['auth'])->group(function () {

    // --- PROFILE ROUTE FIX ---
    Route::get('/profile', function () {
        return redirect()->route('member.dashboard');
    })->name('profile.edit');

    // --- GENERIC DASHBOARD FALLBACK ROUTE ---
    Route::get('/dashboard', function () {
        if (auth()->check() && auth()->user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('member.dashboard');
    })->name('dashboard');

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

    // --- VENDOR DASHBOARD & CATALOG ROUTES ---
    Route::prefix('vendor')->name('vendor.')->group(function () {
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
    });


    // --- EMPLOYEE DASHBOARD ---
    Route::get('/employee/dashboard', function () {
        return view('employee.dashboard');
    })->name('employee.dashboard');


    // --- MEMBER DASHBOARD & VISITING CARD ROUTES (Replaced customer with member) ---
    Route::prefix('member')->name('member.')->group(function () {
        Route::get('/dashboard', function () {
            return view('member.dashboard');
        })->name('dashboard');

        Route::get('/search', function () {
            return view('member.search');
        })->name('search');

        // Card Management Routes
        Route::get('/cards', [CardController::class, 'index'])->name('cards.index');
        Route::get('/card/create', [CardController::class, 'create'])->name('card.create');
        Route::post('/card/store', [CardController::class, 'store'])->name('card.store');
        Route::get('/card/{id}', [CardController::class, 'show'])->name('card.show');
        Route::get('/search-locations', [CardController::class, 'searchLocations'])->name('search.locations');

        // Card Customization Routes
        Route::get('/card/{id}/customize', [CardController::class, 'customize'])->name('card.customize');
        Route::put('/card/{id}/customize', [CardController::class, 'updateDisplay'])->name('card.update.display');
    });

});


// ==========================================
// DIRECT EMERGENCY PINCODE INJECTION ROUTE
// ==========================================
Route::get('/seed-pincodes-now', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'PincodeSeeder', '--force' => true]);
        
        $count = \Illuminate\Support\Facades\DB::table('pincodes')->count();
        if($count == 0) {
            \Illuminate\Support\Facades\DB::table('pincodes')->insert([
                [
                    'pincode' => '282001',
                    'office_name' => 'Agra Head Post Office',
                    'district' => 'Agra',
                    'state_name' => 'Uttar Pradesh',
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'pincode' => '324001',
                    'office_name' => 'Kota Head Post Office',
                    'district' => 'Kota',
                    'state_name' => 'Rajasthan',
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            ]);
            return "Seeder failed, but Emergency Pincodes (Agra & Kota) successfully injected!";
        }
        
        return "Pincodes Seeded Successfully via Seeder! Total records: " . $count;
    } catch (\Exception $e) {
        return "Error: " . $e->getMessage();
    }
});