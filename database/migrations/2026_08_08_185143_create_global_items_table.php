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
    if (!Schema::hasTable('global_items')) {
        Schema::create('global_items', function (Blueprint $table) {
            $table->id();
            $table->string('category');
            $table->string('item_name');
            $table->string('food_type')->default('veg');
            $table->decimal('default_price', 8, 2)->default(0);
            $table->string('status')->default('pending');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
        });
    }
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('global_items');
    }
};
