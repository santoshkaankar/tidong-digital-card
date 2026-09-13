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
    $table->unsignedBigInteger('tax_id')->nullable();
    $table->string('image')->nullable();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('global_items', function (Blueprint $table) {
            //
        });
    }
};
