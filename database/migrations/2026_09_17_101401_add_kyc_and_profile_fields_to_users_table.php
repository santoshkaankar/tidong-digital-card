<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Personal & Profile Fields
            if (!Schema::hasColumn('users', 'profile_photo')) {
                $table->string('profile_photo')->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'gender')) {
                $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('profile_photo');
            }
            if (!Schema::hasColumn('users', 'dob')) {
                $table->date('dob')->nullable()->after('gender');
            }

            // KYC & Identity Verification
            if (!Schema::hasColumn('users', 'pan_number')) {
                $table->string('pan_number', 10)->nullable()->after('dob');
            }
            if (!Schema::hasColumn('users', 'pan_image')) {
                $table->string('pan_image')->nullable()->after('pan_number');
            }
            if (!Schema::hasColumn('users', 'aadhaar_number')) {
                $table->string('aadhaar_number', 12)->nullable()->after('pan_image');
            }
            if (!Schema::hasColumn('users', 'aadhaar_front_image')) {
                $table->string('aadhaar_front_image')->nullable()->after('aadhaar_number');
            }
            if (!Schema::hasColumn('users', 'aadhaar_back_image')) {
                $table->string('aadhaar_back_image')->nullable()->after('aadhaar_front_image');
            }
            if (!Schema::hasColumn('users', 'kyc_status')) {
                $table->enum('kyc_status', ['pending', 'approved', 'rejected', 'unverified'])->default('unverified')->after('aadhaar_back_image');
            }

            // Bank Account Details for Payouts
            if (!Schema::hasColumn('users', 'account_holder_name')) {
                $table->string('account_holder_name')->nullable()->after('kyc_status');
            }
            if (!Schema::hasColumn('users', 'bank_name')) {
                $table->string('bank_name')->nullable()->after('account_holder_name');
            }
            if (!Schema::hasColumn('users', 'account_number')) {
                $table->string('account_number')->nullable()->after('bank_name');
            }
            if (!Schema::hasColumn('users', 'ifsc_code')) {
                $table->string('ifsc_code', 20)->nullable()->after('account_number');
            }
            if (!Schema::hasColumn('users', 'upi_id')) {
                $table->string('upi_id')->nullable()->after('ifsc_code');
            }

            // Address Details
            if (!Schema::hasColumn('users', 'address')) {
                $table->text('address')->nullable()->after('upi_id');
            }
            if (!Schema::hasColumn('users', 'city')) {
                $table->string('city')->nullable()->after('address');
            }
            if (!Schema::hasColumn('users', 'state')) {
                $table->string('state')->nullable()->after('city');
            }
            if (!Schema::hasColumn('users', 'pincode')) {
                $table->string('pincode', 10)->nullable()->after('state');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'profile_photo', 'gender', 'dob',
                'pan_number', 'pan_image', 'aadhaar_number', 'aadhaar_front_image', 'aadhaar_back_image', 'kyc_status',
                'account_holder_name', 'bank_name', 'account_number', 'ifsc_code', 'upi_id',
                'address', 'city', 'state', 'pincode'
            ]);
        });
    }
};