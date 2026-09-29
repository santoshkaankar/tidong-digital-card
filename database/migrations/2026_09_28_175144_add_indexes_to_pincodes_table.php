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
        Schema::table('pincodes', function (Blueprint $table) {
            $table->index('pincode', 'idx_pincode');
            $table->index('office_name', 'idx_office_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pincodes', function (Blueprint $table) {
            $table->dropIndex('idx_pincode');
            $table->dropIndex('idx_office_name');
        });
    }
};