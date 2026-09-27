<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'pickup_otp')) {
                $table->string('pickup_otp', 6)->nullable()->after('status');
            }
            if (!Schema::hasColumn('orders', 'delivery_otp')) {
                $table->string('delivery_otp', 6)->nullable()->after('pickup_otp');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['pickup_otp', 'delivery_otp']);
        });
    }
};