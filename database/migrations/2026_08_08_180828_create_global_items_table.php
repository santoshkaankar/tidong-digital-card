<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('global_items', function (Blueprint $table) {
            $table->id();
            $table->string('category');
            $table->string('food_type')->default('veg');
            $table->string('item_name');
            $table->text('description')->nullable();
            $table->decimal('default_price', 8, 2);
            $table->string('status')->default('approved'); // approved / pending
            $table->unsignedBigInteger('requested_by')->nullable(); // user_id who requested
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('global_items');
    }
};