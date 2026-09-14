<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restaurant_custom_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('restaurant_category_id')->constrained('restaurant_categories')->onDelete('cascade');
            $table->string('name');
            $table->enum('type', ['veg', 'non-veg', 'egg']);
            $table->foreignId('tax_id')->nullable()->constrained('taxes')->onDelete('set null');
            $table->decimal('mrp', 10, 2);
            $table->decimal('price', 10, 2);
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_available')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurant_custom_items');
    }
};