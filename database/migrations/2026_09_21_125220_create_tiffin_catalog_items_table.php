<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tiffin_catalog_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tiffin_catalog_id')->constrained('tiffin_catalogs')->onDelete('cascade');
            $table->string('day'); // Monday, Tuesday, etc.
            $table->string('meal_type'); // breakfast_morning, lunch, dinner, etc.
            
            // Items ko store karne ke liye JSON column (global ya custom item IDs ki list)
            $table->json('item_ids')->nullable(); 
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tiffin_catalog_items');
    }
};