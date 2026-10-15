<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Game Inventories Table (Mining, Raw Materials, Weapons, Vehicles)
        if (!Schema::hasTable('game_inventories')) {
            Schema::create('game_inventories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('item_name'); 
                $table->string('item_type'); // raw_material, weapon, vehicle
                $table->integer('quantity')->default(1);
                $table->integer('durability')->default(100); 
                $table->decimal('value', 10, 2)->default(0.00);
                $table->timestamps();
            });
        }

        // 2. Game Factories Table (Manufacturing Workflow)
        if (!Schema::hasTable('game_factories')) {
            Schema::create('game_factories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
                $table->string('factory_name');
                $table->string('producing_item'); // e.g., 'Truck', 'Gun'
                $table->integer('raw_material_required')->default(10);
                $table->enum('status', ['IDLE', 'PRODUCING', 'COMPLETED'])->default('IDLE');
                $table->timestamps();
            });
        }

        // 3. Game Marketplace Table (OLX Style Resale)
        if (!Schema::hasTable('game_marketplaces')) {
            Schema::create('game_marketplaces', function (Blueprint $table) {
                $table->id();
                $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
                $table->string('item_name');
                $table->integer('condition')->default(100);
                $table->decimal('asking_price', 10, 2);
                $table->enum('status', ['AVAILABLE', 'SOLD'])->default('AVAILABLE');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('game_marketplaces');
        Schema::dropIfExists('game_factories');
        Schema::dropIfExists('game_inventories');
    }
};