<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('global_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('category');
            $table->string('food_type')->default('veg'); // veg, non-veg, egg
            $table->string('item_name');
            $table->string('item_pic')->nullable();
            $table->decimal('mrp', 10, 2)->nullable();
            $table->decimal('default_price', 10, 2);
            $table->text('description')->nullable();
            $table->string('status')->default('approved');
            $table->foreignId('requested_by')->nullable()->constrained('users')->onDelete('set null');
            $table->tinyInteger('is_approved')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('global_items');
    }
};