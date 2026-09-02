<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // 1. Pehla Admin User (Default)
        User::firstOrCreate(
            ['email' => 'admin@tidong.in'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('password123'),
                'role' => 'admin'
            ]
        );

        // 2. Dusra Admin User (Aapke naam se - SANTOSH KUMAR SHARMA)
        User::firstOrCreate(
            ['email' => 'santoshkaankar@gmail.com'],
            [
                'name' => 'SANTOSH KUMARSHARMA',
                'password' => Hash::make('12345678'),
                'role' => 'admin'
            ]
        );

        // Baaki ke seeders (Data safe aur locked rahega, overwrite nahi hoga)
        $this->call([
            CountrySeeder::class,
            PincodeSeeder::class,
            GlobalItemsSeeder::class,
            
        ]);
    }
}