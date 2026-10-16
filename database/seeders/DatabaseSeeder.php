<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // 1. Pehla Admin User (Default)(Aapke naam se - SANTOSH KUMAR SHARMA)
        User::firstOrCreate(
            ['email' => 'santoshkaankar@gmail.com'],
            [
                'name' => 'SANTOSH KUMARSHARMA',
                'password' => Hash::make('12345678'),
                'role' => 'admin'
            ]
        );

        // 2. Customer User Auto Create
        $customer = User::firstOrCreate(
            ['email' => 'meenushona1@gmail.com'],
            [
                'name'          => 'meenu',
                'username'      => 'meenu',
                'mobile'        => '6395392537',
                'role'          => 'member',
                'referral_id'   => 'TDMS6395GSSS',
                'password'      => Hash::make('password'),
                'status'        => 'approved',
                'mobile_verified' => 1,
                'email_verified'  => 1,
            ]
        );

        // 3. Add 46,40,000.00 T-Coins to Wallet
        Wallet::updateOrCreate(
            ['user_id' => $customer->id],
            [
                't_coins'                 => 25000.00,
                'real_balance'            => 0.00,
                'non_withdrawable_balance'=> 0.00,
            ]
        );

        // Baaki ke seeders
        $this->call([
            CountrySeeder::class,
            PincodeSeeder::class,
            GlobalItemsSeeder::class,
            TaxSeeder::class,
            AffiliateStageSeeder::class,
            RetailProductSeeder::class,
        ]);
    }
}