<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('service_type')->default('food')->after('role'); // food, taxi, hotel, money_exchange, emporium, guide
            $table->string('vehicle_no')->nullable()->after('service_type'); // For Taxi Vendor
            $table->string('license_no')->nullable()->after('vehicle_no'); // For Guide / Money Exchange
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['service_type', 'vehicle_no', 'license_no']);
        });
    }
};