<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('vendor_items', function (Blueprint $table) {
        if (!Schema::hasColumn('vendor_items', 'is_available')) {
            $table->boolean('is_available')->default(true)->after('status');
        }
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendor_items', function (Blueprint $table) {
            //
        });
    }
};
