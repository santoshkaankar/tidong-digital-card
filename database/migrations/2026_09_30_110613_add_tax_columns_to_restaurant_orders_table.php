<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTaxColumnsToRestaurantOrdersTable extends Migration
{
    public function up(): void
    {
        Schema::table('restaurant_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('restaurant_orders', 'cgst')) {
                $table->decimal('cgst', 10, 2)->default(0.00)->after('tax_amount');
            }
            if (!Schema::hasColumn('restaurant_orders', 'sgst')) {
                $table->decimal('sgst', 10, 2)->default(0.00)->after('cgst');
            }
        });
    }

    public function down(): void
    {
        Schema::table('restaurant_orders', function (Blueprint $table) {
            if (Schema::hasColumn('restaurant_orders', 'cgst')) {
                $table->dropColumn('cgst');
            }
            if (Schema::hasColumn('restaurant_orders', 'sgst')) {
                $table->dropColumn('sgst');
            }
        });
    }
}