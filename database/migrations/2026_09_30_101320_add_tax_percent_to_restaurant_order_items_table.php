<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('restaurant_order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('restaurant_order_items', 'tax_percent')) {
                $table->decimal('tax_percent', 5, 2)->default(5.00)->after('price');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurant_order_items', function (Blueprint $table) {
            if (Schema::hasColumn('restaurant_order_items', 'tax_percent')) {
                $table->dropColumn('tax_percent');
            }
        });
    }
};