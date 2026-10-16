<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('market_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category'); // phones, cars, clothing, grocery
            $table->decimal('price', 12, 2);
            $table->string('item_type'); // phone, car, shoes, clothes, food, etc.
            $table->string('color_hex')->default('#ffffff');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('market_items');
    }
};