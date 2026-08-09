<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Menus Table (Restaurant ya Hotel ki details)
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('business_name');
            $table->string('whatsapp_number');
            $table->string('type'); // 'restaurant' or 'hotel'
            $table->timestamps();
        });

        // 2. Menu Items Table (Items, Price, aur Description ke sath)
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained()->onDelete('cascade');
            $table->string('item_name');
            $table->text('description')->nullable(); // Item description (Spicy, half/full, etc.)
            $table->decimal('price', 8, 2);
            $table->string('image')->nullable();
            $table->timestamps();
        });

        // 3. Orders Table (Table/Room number, status, aur payment track karne ke liye)
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained()->onDelete('cascade');
            $table->string('table_or_room'); // Jaise: Table-1, Room-102, Reception
            $table->string('status')->default('running'); // running, completed
            $table->string('payment_mode')->nullable(); // cash, online
            $table->string('payment_status')->default('pending'); // pending, paid
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->timestamps();
        });

        // 4. Order Items Table (Order ke andar items aur quantity handle karne ke liye)
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('menu_id')->nullable(); // Optional reference
            $table->string('item_name');
            $table->integer('quantity'); // Dobara order karne ya item badhane par yhi update hoga
            $table->decimal('price', 8, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('menu_items');
        Schema::dropIfExists('menus');
    }
};