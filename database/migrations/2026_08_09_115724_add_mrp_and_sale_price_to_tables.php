<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMrpAndSalePriceToTables extends Migration
{
    public function up(): void
    {
        // Global Items table ke liye
        Schema::table('global_items', function (Blueprint $table) {
            if (!Schema::hasColumn('global_items', 'item_pic')) {
                $table->string('item_pic')->nullable()->after('item_name');
            }
            if (!Schema::hasColumn('global_items', 'mrp')) {
                $table->decimal('mrp', 10, 2)->default(0.00)->after('item_pic');
            }
            if (!Schema::hasColumn('global_items', 'description')) {
                $table->text('description')->nullable()->after('mrp');
            }
        });

        // Business Items table ke liye
        Schema::table('business_items', function (Blueprint $table) {
            if (!Schema::hasColumn('business_items', 'description')) {
                $table->text('description')->nullable()->after('item_name');
            }
            if (!Schema::hasColumn('business_items', 'mrp')) {
                $table->decimal('mrp', 10, 2)->default(0.00)->after('description');
            }
            if (!Schema::hasColumn('business_items', 'sale_price')) {
                $table->decimal('sale_price', 10, 2)->nullable()->after('mrp');
            }
        });
    }

    public function down(): void
    {
        Schema::table('global_items', function (Blueprint $table) {
            $table->dropColumn(['item_pic', 'mrp', 'description']);
        });

        Schema::table('business_items', function (Blueprint $table) {
            $table->dropColumn(['description', 'mrp', 'sale_price']);
        });
    }
}