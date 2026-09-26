<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('restaurant_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('restaurant_orders', 'delivery_otp')) {
                $table->string('delivery_otp', 6)->nullable()->after('status');
            }
            if (!Schema::hasColumn('restaurant_orders', 'payment_status')) {
                $table->string('payment_status')->default('pending')->after('delivery_otp'); // pending, paid
            }
            if (!Schema::hasColumn('restaurant_orders', 'payment_method')) {
                $table->string('payment_method')->default('cod')->after('payment_status'); // cod, online
            }
        });
    }

    public function down(): void
    {
        Schema::table('restaurant_orders', function (Blueprint $table) {
            $table->dropColumn(['delivery_otp', 'payment_status', 'payment_method']);
        });
    }
};