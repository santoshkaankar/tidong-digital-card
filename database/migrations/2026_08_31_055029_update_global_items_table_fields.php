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
        Schema::table('global_items', function (Blueprint $table) {
            // Check & add item_pic column if missing
            if (!Schema::hasColumn('global_items', 'item_pic')) {
                $table->string('item_pic')->nullable()->after('item_name');
            }

            // Check & add mrp column if missing
            if (!Schema::hasColumn('global_items', 'mrp')) {
                $table->decimal('mrp', 10, 2)->nullable()->after('item_pic');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('global_items', function (Blueprint $table) {
            if (Schema::hasColumn('global_items', 'item_pic')) {
                $table->dropColumn('item_pic');
            }
            if (Schema::hasColumn('global_items', 'mrp')) {
                $table->dropColumn('mrp');
            }
        });
    }
};