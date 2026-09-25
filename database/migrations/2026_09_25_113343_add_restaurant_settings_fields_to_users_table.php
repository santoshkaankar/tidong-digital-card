<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'food_type')) {
                $table->string('food_type')->nullable()->after('business_type');
            }
            if (!Schema::hasColumn('users', 'area')) {
                $table->string('area')->nullable()->after('address');
            }
            if (!Schema::hasColumn('users', 'gstin')) {
                $table->string('gstin')->nullable()->after('pincode');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['food_type', 'area', 'gstin']);
        });
    }
};