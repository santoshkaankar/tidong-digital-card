<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('restaurant_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('restaurant_orders', 'delivery_boy_id')) {
                $table->foreignId('delivery_boy_id')->nullable()->after('user_id')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('restaurant_orders', 'delivery_fee')) {
                $table->decimal('delivery_fee', 8, 2)->default(0.00);
            }
            if (!Schema::hasColumn('restaurant_orders', 'delivered_at')) {
                $table->timestamp('delivered_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('restaurant_orders', function (Blueprint $table) {$table->dropColumn(['delivery_boy_id', 'delivery_fee', 'delivered_at']);
        });
    }
};