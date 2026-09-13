<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('restaurant_items') && !Schema::hasColumn('restaurant_items', 'tax_id')) {
            Schema::table('restaurant_items', function (Blueprint $table) {
                $table->unsignedBigInteger('tax_id')->nullable()->after('id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('restaurant_items') && Schema::hasColumn('restaurant_items', 'tax_id')) {
            Schema::table('restaurant_items', function (Blueprint $table) {
                $table->dropColumn('tax_id');
            });
        }
    }
};