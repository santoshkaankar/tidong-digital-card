<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
{
    \App\Models\User::firstOrCreate(
        ['email' => 'santoshkaankar@gmail.com'], // Yahan apna email likhein
        [
            'name' => 'Santosh Sharma', // Yahan apna naam likhein
            'username' => 'santosh',            // Aapka alag se username (agar column hai toh)
            'password' => bcrypt('password'), // Yahan apna password likhein
        ]
    );
}
}
