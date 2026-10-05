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
    Schema::table('restaurant_items', function (Blueprint $table) {
        $table->boolean('is_active')->default(true)->after('price'); // Hide / Show
        $table->boolean('in_stock')->default(true)->after('is_active'); // Stock status
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            //
        });
    }
};
