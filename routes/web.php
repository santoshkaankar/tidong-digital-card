<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\UpdateController;
use App\Http\Controllers\CardController;
use Illuminate\Support\Facades\File;

// ==========================================
// UTILITY & SYSTEM INITIALIZATION ROUTES
// ==========================================

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

Route::get('/run-fresh-migration', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate:fresh', ['--seed' => true, '--force' => true]);
        return "<h1>Success!</h1><p>Database fresh migrate ho gaya hai aur seeders chal chuke hain.</p><p><a href='/login'>Go to Login</a></p>";
    } catch (\Exception $e) {
        return "<h1>Error:</h1> " . $e->getMessage();
    }
});

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

Route::get('/fix-and-setup-db', function () {
    try {
        User::firstOrCreate(
            ['email' => 'admin@tidong.in'],
            ['name' => 'System Admin', 'password' => Hash::make('password123'), 'role' => 'admin']
        );

        User::firstOrCreate(
            ['email' => 'santoshkaankar@gmail.com'],
            ['name' => 'SANTOSH KUMAR SHARMA', 'password' => Hash::make('12345678'), 'role' => 'admin']
        );

        $existingCount = \Illuminate\Support\Facades\DB::table('pincodes')->count();
        if ($existingCount == 0) {
            $seeder = new \Database\Seeders\PincodeSeeder();
            $seeder->run();
            return "Success! Dono Admin ban gaye aur Pincodes safely seed ho gaye.";
        }

        return "Database already locked & safe! Dono Admin active hain.";
    } catch (\Exception $e) {
        return "Error: " . $e->getMessage();
    }
});

Route::get('/setup-live-database', function () {
    return "<h1>Website is Live & Running! Database is locked & secure.</h1><p><a href='/login'>Go to Login</a></p>";
});

Route::get('/clean-migration', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate:rollback', ['--force' => true]);
        return "Migration rolled back successfully! Naye columns hata diye gaye hain.";
    } catch (\Exception $e) {
        return "Error: " . $e->getMessage();
    }
});

// Emergency Seed Route
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


// ==========================================
// PUBLIC & AUTHENTICATION ROUTES
// ==========================================

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Public Digital Card Link Engine (Public Access) - Fixed Route Name
Route::get('/card/v/{slug}', [CardController::class, 'showPublic'])->name('member.card.public');
Route::get('/card/{id}', [CardController::class, 'show'])->name('card.show');

Route::get('/search-locations', [CardController::class, 'searchLocations'])->name('search.locations');
Route::get('/menu/{slug}', [MenuController::class, 'showPublicMenu'])->name('menu.public');
Route::post('/menu/{slug}/order', [MenuController::class, 'placeOrder'])->name('menu.order');
Route::post('/order/{orderId}/complete', [MenuController::class, 'completeOrder'])->name('order.complete');


// ==========================================
// AUTHENTICATED & ROLE-BASED DASHBOARDS
// ==========================================

Route::middleware(['auth'])->group(function () {

    Route::get('/', function () {
    if (auth()->check()) {
        $role = auth()->user()->role;

        if ($role === 'admin') {
            return redirect()->route('admin.dashboard');
        } 
        
        // Agar employee ya vendor ke routes defined nahi hain, toh member dashboard par bhej denge ya check laga lenge
        if ($role === 'employee' && \Route::has('employee.dashboard')) {
            return redirect()->route('employee.dashboard');
        } 
        
        if ($role === 'vendor' && \Route::has('vendor.dashboard')) {
            return redirect()->route('vendor.dashboard');
        }

        // Default member dashboard
        return redirect()->route('member.dashboard');
    }
    
    return view('welcome');
});

    // --- ADMIN ROUTES ---
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
        Route::delete('/delete/{id}', [AdminController::class, 'destroy'])->name('delete');
        Route::post('/register-user', [AdminController::class, 'storeUser'])->name('store.user');

        Route::get('/pending-items', [AdminController::class, 'pendingItems'])->name('pending.items');
        Route::post('/item/approve/{id}', [AdminController::class, 'approveItem'])->name('item.approve');
        Route::delete('/item/reject/{id}', [AdminController::class, 'rejectItem'])->name('item.reject');
        
        Route::get('/global-item/create', [AdminController::class, 'createGlobalItem'])->name('global.item.create');
        Route::post('/global-item', [AdminController::class, 'storeGlobalItem'])->name('global.item.store');
        Route::post('/item-category/store', [AdminController::class, 'storeItemCategory'])->name('item.category.store');

        Route::get('/vendor-categories', [AdminController::class, 'createVendorCategory'])->name('vendor.categories');
        Route::post('/vendor-categories', [AdminController::class, 'storeVendorCategory'])->name('vendor.categories.store');
        Route::delete('/vendor-categories/{id}', [AdminController::class, 'destroyVendorCategory'])->name('vendor.categories.delete');

        Route::get('/update', [UpdateController::class, 'showUploadForm'])->name('update');
        Route::post('/update', [UpdateController::class, 'uploadUpdate'])->name('upload_update');

        Route::get('/create-card', [CardController::class, 'create'])->name('card.create');
        Route::post('/store-card', [CardController::class, 'store'])->name('card.store');
        Route::get('/card/{id}/edit', [CardController::class, 'edit'])->name('card.edit');
        Route::post('/card/{id}/update', [CardController::class, 'update'])->name('card.update');

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

    // --- MEMBER DASHBOARD & DIGITAL CARD ROUTES (FULL COMBINED SET) ---
    Route::prefix('member')->name('member.')->group(function () {
        Route::get('/dashboard', function () {
            return view('member.dashboard');
        })->name('dashboard');

        Route::get('/search', function () {
            return view('member.search');
        })->name('search');

        // Card Indexing & Dashboards
        Route::get('/cards', [CardController::class, 'index'])->name('cards.index');
        Route::get('/cards-list', [CardController::class, 'index'])->name('card.index');
        
        // Master Profile Form Routes
        Route::get('/card/configure', [CardController::class, 'configure'])->name('card.configure');
        Route::post('/card/configure', [CardController::class, 'storeMaster'])->name('card.configure.store');

        // Multi-Theme Variant Creation & Action Routes
        Route::get('/card/view/create', [CardController::class, 'createDesign'])->name('card.view.create');
        Route::post('/card/view/store', [CardController::class, 'storeDesign'])->name('card.view.store'); // Yahan POST route added hai
        Route::delete('/card/view/{id}', [CardController::class, 'destroyView'])->name('card.view.destroy');
        
        // Direct CRUD Operations for Visiting Cards (Legacy & Form Submissions)
        Route::get('/card/create', [CardController::class, 'create'])->name('card.create');
        Route::post('/card/store', [CardController::class, 'store'])->name('card.store');
        Route::get('/card/{id}/edit', [CardController::class, 'edit'])->name('card.edit');
        Route::post('/card/{id}/update', [CardController::class, 'update'])->name('card.update.post'); // POST support for forms
        Route::put('/card/{id}', [CardController::class, 'update'])->name('card.update');              // PUT support for RESTful forms
        Route::delete('/card/{id}', [CardController::class, 'destroy'])->name('card.destroy');
    });

});

Route::get('/create-folders', function () {
    $photosPath = public_path('uploads/photos');
    $qrcodesPath = public_path('uploads/qrcodes');

    if (!File::exists($photosPath)) {
        File::makeDirectory($photosPath, 0755, true);
    }

    if (!File::exists($qrcodesPath)) {
        File::makeDirectory($qrcodesPath, 0755, true);
    }

    return "Folders successfully created!";
});