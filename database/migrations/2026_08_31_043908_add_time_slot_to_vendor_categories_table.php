<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vendor_categories', function (Blueprint $table) {
            if (!Schema::hasColumn('vendor_categories', 'time_slot')) {
                $table->enum('time_slot', ['morning', 'afternoon', 'evening', 'all_day'])->default('all_day')->after('name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('vendor_categories', function (Blueprint $table) {
            $table->dropColumn('time_slot');
        });
    }
};