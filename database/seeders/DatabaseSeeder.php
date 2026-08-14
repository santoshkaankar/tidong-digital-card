<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'santoshkaankar@gmail.com'],
            [
                'name' => 'SANTOSH KUMAR SHARMA',
                'username' => 'santosh',
                'password' => Hash::make('San@#$321'), // Apna secure password
                'role' => 'admin',
                'status' => 'active'
            ]
        );

        // Baaki ke seeders jo aapke project me zaroori hain
        $this->call([
            CountrySeeder::class,
            PincodeSeeder::class,
        ]);
    }
}