<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\Member\CardController;

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
    $user = \App\Models\User::where('email', 'admin@tidong.in')->first();
    
    if ($user) {
        $user->password = \Illuminate\Support\Facades\Hash::make('password123');
        $user->save();
        return "Admin Password Reset to 'password123'. Ab login try karein.";
    } else {
        \App\Models\User::create([
            'name' => 'Santosh Kumar Sharma',
            'email' => 'admin@tidong.in',
            'password' => \Illuminate\Support\Facades\Hash::make('password123'),
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

        \App\Models\User::firstOrCreate(
            ['email' => 'admin@tidong.in'],
            [
                'name' => 'Santosh Kumar Sharma',
                'password' => \Illuminate\Support\Facades\Hash::make('password123'),
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
        \App\Models\User::firstOrCreate(
            ['email' => 'admin@tidong.in'],
            ['name' => 'System Admin', 'password' => \Illuminate\Support\Facades\Hash::make('password123'), 'role' => 'admin']
        );

        \App\Models\User::firstOrCreate(
            ['email' => 'santoshkaankar@gmail.com'],
            ['name' => 'SANTOSH KUMAR SHARMA', 'password' => \Illuminate\Support\Facades\Hash::make('12345678'), 'role' => 'admin']
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

// ==========================================
// PUBLIC & AUTHENTICATION ROUTES (Loaded from auth.php)
// ==========================================
require __DIR__ . '/auth.php';

// Public Digital Card Link Engine & Menus
Route::get('/card/v/{slug}', [CardController::class, 'showPublic'])->name('member.card.public');
Route::get('/card/{slug}', [CardController::class, 'showPublic'])->name('card.show');
Route::get('/search-locations', [CardController::class, 'searchLocations'])->name('search.locations');
Route::get('/menu/{slug}', [MenuController::class, 'showPublicMenu'])->name('menu.public');
Route::post('/menu/{slug}/order', [MenuController::class, 'placeOrder'])->name('menu.order');
Route::post('/order/{orderId}/complete', [MenuController::class, 'completeOrder'])->name('order.complete');

// ==========================================
// AUTHENTICATED & ROLE-BASED GROUPS
// ==========================================
Route::middleware(['auth'])->group(function () {

    // --- ADMIN ROUTES ---
    require __DIR__ . '/admin.php';

    // --- VENDOR ROUTES ---
    Route::prefix('vendor')->name('vendor.')->group(base_path('routes/vendor.php'));

    // --- EMPLOYEE DASHBOARD ---
    Route::get('/employee/dashboard', function () {
        return view('employee.dashboard');
    })->name('employee.dashboard');

    // --- MEMBER ROUTES ---
    Route::prefix('member')->name('member.')->group(base_path('routes/member.php'));

});