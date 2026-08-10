Route::get('/setup-live-database', function () {
    try {
        $dbPath = '/var/data/database.sqlite';
        if (!file_exists(dirname($dbPath))) {
            mkdir(dirname($dbPath), 0755, true);
        }
        if (!file_exists($dbPath)) {
            touch($dbPath);
        }

        Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        
        $email = 'santoshkaankar@gmail.com';
        if (!App\Models\User::where('email', $email)->exists()) {
            App\Models\User::create([
                'name' => 'Admin',
                'email' => $email,
                'role' => 'admin',
                'password' => Illuminate\Support\Facades\Hash::make('password')
            ]);
        }

        app(Database\Seeders\PincodeSeeder::class)->run();

        return "<h1>Success! Database ready and Pincodes Seeded!</h1><p><a href='/login'>Go to Login</a></p>";
    } catch (\Exception $e) {
        return "<h1>Error:</h1><p>" . $e->getMessage() . "</p>";
    }
});