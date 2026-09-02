<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Device & Dynamic Session Identification Table
        Schema::create('guest_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('device_uuid')->unique(); // Dynamic Browser/Device Fingerprint ID
            $table->string('language')->default('en'); // User Selected/Detected Language
            $table->unsignedBigInteger('vendor_id')->nullable(); // Last Scanned QR Vendor
            $table->string('last_table_or_room')->nullable(); // Table No or Room No
            $table->ipAddress('ip_address')->nullable();
            $table->timestamps();
        });

        // 2. Global Services Master Table (Food, Taxi, Hotel, Money Exchange etc.)
        Schema::create('tidong_services', function (Blueprint $table) {
            $table->id();
            $table->string('service_code')->unique(); // e.g. food, taxi, hotel, money_exchange
            $table->string('name_en');
            $table->string('name_hi');
            $table->string('icon_class'); // FontAwesome Icon
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guest_sessions');
        Schema::dropIfExists('tidong_services');
    }
};