<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('grocery_products', function (Blueprint $table): void {
            $table->string('brand', 100)->nullable();
            $table->text('description')->nullable();
            $table->string('image_url', 1500)->nullable();
            $table->boolean('is_featured')->default(false);
        });

        Schema::create('grocery_online_orders', function (Blueprint $table): void {
            $table->id();
            $table->string('order_number', 50)->unique();
            $table->string('checkout_token', 64)->unique();
            $table->foreignId('business_id')->constrained('businesses')->restrictOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->restrictOnDelete();
            $table->foreignId('customer_user_id')->constrained('users')->restrictOnDelete();
            $table->string('status', 32)->default('pending');
            $table->string('fulfilment_type', 32); // pickup or delivery_request: NOT dispatched by delivery app
            $table->string('payment_method', 32)->default('pay_at_handover'); // no payment captured
            $table->string('payment_status', 32)->default('unpaid');
            $table->string('contact_name', 120);
            $table->string('contact_phone', 25);
            $table->string('address_line', 250)->nullable();
            $table->string('city', 120)->nullable();
            $table->string('postal_code', 15)->nullable();
            $table->string('supply_state', 100);
            $table->string('branch_state_snapshot', 100);
            $table->boolean('interstate');
            $table->text('customer_note')->nullable();
            $table->unsignedBigInteger('taxable_paise');
            $table->unsignedBigInteger('cgst_paise');
            $table->unsignedBigInteger('sgst_paise');
            $table->unsignedBigInteger('igst_paise');
            $table->unsignedBigInteger('tax_paise');
            $table->unsignedBigInteger('discount_paise')->default(0);
            $table->unsignedBigInteger('total_paise');
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
            $table->index(['branch_id', 'status', 'created_at'], 'grocery_online_branch_status_idx');
            $table->index(['customer_user_id', 'created_at'], 'grocery_online_customer_idx');
        });

        Schema::create('grocery_online_order_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_id')->constrained('grocery_online_orders')->restrictOnDelete();
            $table->foreignId('product_id')->constrained('grocery_products')->restrictOnDelete();
            $table->string('sku_snapshot', 64);
            $table->string('name_snapshot', 180);
            $table->string('unit_snapshot', 10);
            $table->string('hsn_snapshot', 12)->nullable();
            $table->unsignedBigInteger('quantity_milli');
            $table->unsignedBigInteger('unit_mrp_paise');
            $table->unsignedBigInteger('unit_sale_paise');
            $table->unsignedBigInteger('discount_paise')->default(0);
            $table->unsignedSmallInteger('gst_rate_bps_snapshot');
            $table->boolean('tax_inclusive_snapshot');
            $table->unsignedBigInteger('taxable_paise');
            $table->unsignedBigInteger('cgst_paise');
            $table->unsignedBigInteger('sgst_paise');
            $table->unsignedBigInteger('igst_paise');
            $table->unsignedBigInteger('tax_paise');
            $table->unsignedBigInteger('total_paise');
            $table->timestamp('created_at')->useCurrent();
            $table->unique(['order_id', 'product_id'], 'grocery_online_order_product_unique');
        });

        Schema::create('grocery_online_order_allocations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_id')->constrained('grocery_online_orders')->restrictOnDelete();
            $table->foreignId('product_id')->constrained('grocery_products')->restrictOnDelete();
            $table->foreignId('lot_id')->constrained('grocery_stock_lots')->restrictOnDelete();
            $table->unsignedBigInteger('quantity_milli');
            $table->timestamp('created_at')->useCurrent();
            $table->index(['order_id', 'lot_id'], 'grocery_allocations_order_lot_idx');
        });

        Schema::create('grocery_online_order_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_id')->constrained('grocery_online_orders')->restrictOnDelete();
            $table->foreignId('actor_user_id')->constrained('users')->restrictOnDelete();
            $table->string('from_status', 32)->nullable();
            $table->string('to_status', 32);
            $table->string('note', 500)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['order_id', 'created_at'], 'grocery_order_events_order_idx');
        });

        // New website features deliberately OFF until explicitly approved by Super Admin.
        foreach (['online_store', 'online_orders'] as $flag) {
            DB::table('grocery_feature_flags')->updateOrInsert(
                ['feature' => $flag],
                ['enabled' => false, 'updated_at' => now(), 'created_at' => now()]
            );
        }
    }

    public function down(): void
    {
        throw new RuntimeException('Grocery online orders and inventory ledger must be preserved. Rollback requires an approved retention plan.');
    }
};
