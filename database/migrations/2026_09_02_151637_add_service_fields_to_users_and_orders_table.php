<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'duty_status')) {
                $table->enum('duty_status', ['online', 'offline', 'busy'])->default('offline')->after('business_type');
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'service_category')) {
                $table->string('service_category')->nullable()->default('food');
            }
            if (!Schema::hasColumn('orders', 'pickup_location')) {
                $table->string('pickup_location')->nullable();
            }
            if (!Schema::hasColumn('orders', 'drop_location')) {
                $table->string('drop_location')->nullable();
            }
            if (!Schema::hasColumn('orders', 'booking_date')) {
                $table->dateTime('booking_date')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'duty_status')) {
                $table->dropColumn('duty_status');
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['service_category', 'pickup_location', 'drop_location', 'booking_date']);
        });
    }
};