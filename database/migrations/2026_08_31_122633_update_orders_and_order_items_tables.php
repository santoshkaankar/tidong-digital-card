<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Orders Table Updates
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->onDelete('cascade');
            }
            if (!Schema::hasColumn('orders', 'location_label')) {
                $table->string('location_label')->nullable()->after('table_or_room');
            }
            // menu_id ko nullable banane ke liye constraint check & modify
            if (Schema::hasColumn('orders', 'menu_id')) {
                try {
                    $table->dropForeign(['menu_id']);
                } catch (\Exception $e) {
                    // Foreign key na hone par catch karein
                }
                $table->unsignedBigInteger('menu_id')->nullable()->change();
            }
        });

        // 2. Order Items Table Updates
        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'item_id')) {
                $table->unsignedBigInteger('item_id')->nullable()->after('menu_id');
            }
            // order_items me bhi menu_id ko nullable banayein
            if (Schema::hasColumn('order_items', 'menu_id')) {
                try {
                    $table->dropForeign(['menu_id']);
                } catch (\Exception $e) {
                    // Foreign key na hone par catch karein
                }
                $table->unsignedBigInteger('menu_id')->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
            if (Schema::hasColumn('orders', 'location_label')) {
                $table->dropColumn('location_label');
            }
        });

        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'item_id')) {
                $table->dropColumn('item_id');
            }
        });
    }
};