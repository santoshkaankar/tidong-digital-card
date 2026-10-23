<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            if (! Schema::hasColumn('users', 'mobile')) {
                $table->string('mobile', 20)->nullable()->unique();
            }
            if (! Schema::hasColumn('users', 'username')) {
                $table->string('username', 40)->nullable()->unique();
            }
        });

        // Retain existing account-type and role definitions. Add missing public identities only.
        foreach ([
            ['vendor', 'Vendor'],
            ['delivery', 'Delivery Partner'],
            ['professional', 'Professional'],
            ['service_provider', 'Service Provider'],
            ['customer', 'Customer / Member'],
        ] as [$code, $name]) {
            if (! DB::table('account_types')->where('code', $code)->exists()) {
                DB::table('account_types')->insert([
                    'code' => $code, 'name' => $name, 'is_active' => true,
                    'created_at' => now(), 'updated_at' => now(),
                ]);
            }
        }
        foreach ([
            ['vendor_owner', 'Vendor (Admin)'],
            ['delivery_partner', 'Delivery Partner'],
            ['professional', 'Professional'],
            ['service_provider', 'Service Provider'],
            ['customer', 'Customer / Member'],
        ] as [$code, $name]) {
            if (! DB::table('roles')->where('code', $code)->exists()) {
                DB::table('roles')->insert([
                    'code' => $code, 'name' => $name, 'description' => 'Registration identity role',
                    'is_system' => true, 'created_at' => now(), 'updated_at' => now(),
                ]);
            }
        }
        Schema::create('registration_affiliate_intents', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->restrictOnDelete();
            $table->string('referral_code', 16)->nullable();
            $table->char('placement_leg', 1)->default('A');
            $table->string('status', 24)->default('pending_verification');
            $table->string('last_error', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        // Rule 2: Never delete identities or affiliate records during an automatic rollback.
        throw new RuntimeException('Registration identity and affiliate history require an approved recovery plan.');
    }
};
