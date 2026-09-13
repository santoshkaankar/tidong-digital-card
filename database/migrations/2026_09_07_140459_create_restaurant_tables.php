<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 0. Global Taxes Master Table (For Multi-Business & Multi-Vendor)
        Schema::create('taxes', function (Blueprint $table) {
            $table->id();
            $table->string('tax_name'); // e.g., GST 0%, GST 5%, GST 18%, CESS
            $table->decimal('tax_percentage', 5, 2)->default(0.00);
            $table->decimal('cess_percentage', 5, 2)->default(0.00);
            $table->string('tax_type')->default('GST'); // GST, VAT, Service Tax
            $table->boolean('is_active')->default(true);
            $table->text('remark')->nullable();
            $table->timestamps();
        });

        // 1. Restaurant Tables
        Schema::create('restaurant_tables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('table_number'); // e.g., "Table No 1", "Room 101"
            $table->integer('seating_capacity')->default(4);
            $table->string('qr_code_token')->nullable()->unique();
            $table->string('qr_code_image')->nullable();
            $table->json('selected_items')->nullable();
            $table->unsignedBigInteger('current_order_id')->nullable();
            $table->enum('status', ['available', 'occupied', 'reserved'])->default('available');
            $table->text('remark')->nullable();
            $table->timestamps();
        });

        // 2. Restaurant Categories
        Schema::create('restaurant_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained('item_categories')->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->nullable();
            $table->string('image')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('status')->default(true);
            $table->text('remark')->nullable();
            $table->timestamps();
        });

        // 3. Restaurant Items (Linked with Global Tax Master)
        Schema::create('restaurant_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('global_item_id')->constrained('global_items')->onDelete('cascade');
            $table->foreignId('restaurant_category_id')->nullable()->constrained('restaurant_categories')->onDelete('cascade');
            $table->foreignId('tax_id')->nullable()->constrained('taxes')->nullOnDelete(); // Linked Tax Slab
            
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->enum('item_type', ['veg', 'non_veg', 'egg'])->default('veg');
            
            $table->decimal('mrp', 10, 2)->default(0);
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('tax_rate', 5, 2)->default(0); // Legacy/Backup rate
            
            $table->boolean('is_available')->default(true);
            $table->boolean('status')->default(true);
            $table->text('remark')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'global_item_id']);
        });

        // 4. Restaurant Orders
        Schema::create('restaurant_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('customer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('table_id')->nullable()->constrained('restaurant_tables')->onDelete('set null');
            
            $table->string('order_number')->unique();
            $table->string('order_type')->default('dine_in');
            
            $table->boolean('is_guest')->default(true);
            $table->string('guest_session_id')->nullable()->index();
            $table->string('device_ip', 45)->nullable();
            
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();

            $table->decimal('sub_total', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0); // Total Tax Calculated
            $table->decimal('tip_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            
            $table->string('currency_code', 3)->default('INR');
            $table->decimal('exchange_rate', 10, 4)->default(1.0000);
            $table->decimal('converted_amount', 10, 2)->nullable();
            
            $table->enum('status', ['pending', 'cooking', 'served', 'completed', 'cancelled'])->default('pending');
            $table->enum('payment_status', ['unpaid', 'paid', 'refunded'])->default('unpaid');
            $table->enum('payment_method', ['cash', 'online', 'upi', 'card'])->nullable();
            $table->enum('payment_request_status', ['none', 'cash_requested', 'online_requested', 'done'])->default('none');
            
            $table->string('transaction_id')->nullable();
            $table->string('payment_proof')->nullable();
            
            $table->text('notes')->nullable();
            $table->text('remark')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        // 5. Restaurant Order Items
        Schema::create('restaurant_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('restaurant_orders')->onDelete('cascade');
            $table->foreignId('item_id')->nullable()->constrained('restaurant_items')->onDelete('set null');
            $table->string('item_name');
            $table->integer('quantity')->default(1);
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('subtotal', 10, 2)->default(0);
            
            $table->integer('batch_number')->default(1);
            $table->enum('kitchen_status', ['sent_to_kitchen', 'cooking', 'ready', 'served'])->default('sent_to_kitchen');
            $table->text('item_notes')->nullable();
            $table->text('remark')->nullable();
            
            $table->timestamps();
        });

        // 6. Waiter Call Requests
        Schema::create('waiter_calls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('table_id')->constrained('restaurant_tables')->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained('restaurant_orders')->onDelete('cascade');
            $table->string('guest_session_id')->nullable();
            $table->enum('call_type', ['call_waiter', 'request_bill', 'water_request', 'pay_bill_cash', 'bill_cash', 'cash_payment', 'cash_requested'])->default('call_waiter');
            $table->enum('status', ['pending', 'attended'])->default('pending');
            $table->text('remark')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('waiter_calls');
        Schema::dropIfExists('restaurant_order_items');
        Schema::dropIfExists('restaurant_orders');
        Schema::dropIfExists('restaurant_items');
        Schema::dropIfExists('restaurant_categories');
        Schema::dropIfExists('restaurant_tables');
        Schema::dropIfExists('taxes');
    }
};