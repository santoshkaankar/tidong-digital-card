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
    Schema::create('proceed_tiffin_orders', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('vendor_id');
        $table->string('duration'); // 1d, 1w, 1m, custom
        $table->date('from_date')->nullable();
        $table->date('to_date')->nullable();
        $table->text('meal_types')->nullable();
        $table->text('selected_catalogs')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proceed_tiffin_orders');
    }
};
