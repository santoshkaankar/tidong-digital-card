<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Retail Categories Table
        Schema::create('retail_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->foreignId('parent_id')->nullable()->constrained('retail_categories')->onDelete('cascade');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Retail Products Table
        Schema::create('retail_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vendor_id');
            $table->foreignId('category_id')->constrained('retail_categories')->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('description');
            $table->decimal('price', 10, 2);
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->integer('stock')->default(0);
            $table->string('sku')->unique();
            $table->json('images')->nullable();
            $table->enum('status', ['draft', 'published', 'out_of_stock'])->default('published');
            $table->timestamps();
        });

        // 3. Retail Orders Table
        Schema::create('retail_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('vendor_id');
            $table->decimal('total_amount', 10, 2);
            $table->enum('status', ['pending', 'processing', 'shipped', 'delivered', 'cancelled'])->default('pending');
            $table->string('payment_method');
            $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending');
            $table->text('shipping_address');
            $table->timestamps();
        });

        // 4. Retail Order Items Table
        Schema::create('retail_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('retail_orders')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('retail_products')->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('retail_order_items');
        Schema::dropIfExists('retail_orders');
        Schema::dropIfExists('retail_products');
        Schema::dropIfExists('retail_categories');
    }
};