<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grocery_feature_flags', function (Blueprint $table): void {
            $table->string('feature', 50)->primary();
            $table->boolean('enabled')->default(false);
            $table->foreignId('updated_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });

        Schema::create('grocery_branch_features', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->restrictOnDelete();
            $table->string('feature', 50);
            $table->boolean('enabled')->default(true);
            $table->foreignId('updated_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
            $table->unique(['branch_id', 'feature']);
        });

        Schema::create('grocery_products', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses')->restrictOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->restrictOnDelete();
            $table->string('sku', 64);
            $table->string('barcode', 80)->nullable();
            $table->string('name', 180);
            $table->string('category', 100);
            $table->string('unit', 10); // pcs, pack, kg, g, l, ml
            $table->string('hsn_code', 12)->nullable();
            $table->unsignedSmallInteger('gst_rate_bps')->default(0); // 18.00% = 1800
            $table->boolean('tax_inclusive')->default(true);
            $table->unsignedBigInteger('offline_mrp_paise');
            $table->unsignedBigInteger('offline_sale_paise');
            $table->unsignedBigInteger('online_mrp_paise')->nullable();
            $table->unsignedBigInteger('online_sale_paise')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
            $table->unique(['branch_id', 'sku'], 'grocery_product_branch_sku_unique');
            $table->unique(['branch_id', 'barcode'], 'grocery_product_branch_barcode_unique');
            $table->index(['business_id', 'branch_id', 'is_active'], 'grocery_products_scope_idx');
        });

        Schema::create('grocery_stock_lots', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses')->restrictOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->restrictOnDelete();
            $table->foreignId('product_id')->constrained('grocery_products')->restrictOnDelete();
            $table->string('lot_code', 80);
            $table->date('expiry_date')->nullable();
            $table->unsignedBigInteger('received_qty_milli');
            $table->unsignedBigInteger('available_qty_milli');
            $table->boolean('is_sellable')->default(true);
            $table->timestamps();
            $table->index(['product_id', 'is_sellable', 'expiry_date'], 'grocery_lots_sellable_idx');
        });

        Schema::create('grocery_sales', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses')->restrictOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->restrictOnDelete();
            $table->string('sale_number', 50)->unique();
            $table->string('status', 20)->default('recorded');
            $table->string('supply_state', 100);
            $table->string('branch_state_snapshot', 100);
            $table->boolean('interstate');
            $table->unsignedBigInteger('taxable_paise');
            $table->unsignedBigInteger('cgst_paise');
            $table->unsignedBigInteger('sgst_paise');
            $table->unsignedBigInteger('igst_paise');
            $table->unsignedBigInteger('tax_paise');
            $table->unsignedBigInteger('total_paise');
            $table->foreignId('recorded_by')->constrained('users')->restrictOnDelete();
            $table->timestamp('reversed_at')->nullable();
            $table->timestamps();
            $table->index(['business_id', 'branch_id', 'created_at'], 'grocery_sales_scope_idx');
        });

        Schema::create('grocery_sale_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('sale_id')->constrained('grocery_sales')->restrictOnDelete();
            $table->foreignId('product_id')->constrained('grocery_products')->restrictOnDelete();
            $table->string('sku_snapshot', 64);
            $table->string('name_snapshot', 180);
            $table->string('unit_snapshot', 10);
            $table->string('hsn_snapshot', 12)->nullable();
            $table->unsignedBigInteger('quantity_milli');
            $table->unsignedBigInteger('unit_mrp_paise');
            $table->unsignedBigInteger('unit_sale_paise');
            $table->unsignedBigInteger('discount_paise')->default(0);
            $table->unsignedSmallInteger('discount_rate_bps_snapshot')->default(0);
            $table->unsignedSmallInteger('gst_rate_bps_snapshot');
            $table->boolean('tax_inclusive_snapshot');
            $table->unsignedBigInteger('taxable_paise');
            $table->unsignedBigInteger('cgst_paise');
            $table->unsignedBigInteger('sgst_paise');
            $table->unsignedBigInteger('igst_paise');
            $table->unsignedBigInteger('tax_paise');
            $table->unsignedBigInteger('total_paise');
            $table->timestamps();
        });

        Schema::create('grocery_sale_reversals', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('sale_id')->unique()->constrained('grocery_sales')->restrictOnDelete();
            $table->foreignId('actor_user_id')->constrained('users')->restrictOnDelete();
            $table->text('reason');
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('grocery_stock_movements', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses')->restrictOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->restrictOnDelete();
            $table->foreignId('product_id')->constrained('grocery_products')->restrictOnDelete();
            $table->foreignId('lot_id')->constrained('grocery_stock_lots')->restrictOnDelete();
            $table->foreignId('sale_id')->nullable()->constrained('grocery_sales')->restrictOnDelete();
            $table->string('movement_type', 32);
            $table->bigInteger('delta_qty_milli'); // immutable ledger; negative for outbound
            $table->string('reason', 500)->nullable();
            $table->foreignId('actor_user_id')->constrained('users')->restrictOnDelete();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['business_id', 'branch_id', 'product_id'], 'grocery_movements_scope_idx');
        });
    }

    public function down(): void
    {
        // Safety: never leave the migration marked as rolled-back while the immutable
        // Grocery financial/stock history still exists. An explicit retention plan is required.
        throw new RuntimeException('Grocery migration rollback blocked: preserve sale and stock history.');
    }
};
