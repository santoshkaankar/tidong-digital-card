<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sublimation_products', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses')->restrictOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->restrictOnDelete();
            $table->string('name', 160);
            $table->string('category', 80);
            $table->text('production_notes')->nullable();
            $table->unsignedBigInteger('blank_cost_paise')->default(0);
            $table->unsignedBigInteger('print_cost_paise')->default(0);
            $table->unsignedBigInteger('packing_cost_paise')->default(0);
            $table->unsignedBigInteger('other_cost_paise')->default(0);
            $table->unsignedBigInteger('sale_price_paise')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['business_id', 'branch_id', 'is_active'], 'subl_prod_business_branch_idx');
        });

        Schema::create('sublimation_orders', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses')->restrictOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->restrictOnDelete();
            $table->foreignId('product_id')->constrained('sublimation_products')->restrictOnDelete();
            $table->string('order_no', 50)->unique();
            $table->string('customer_name', 160);
            $table->string('customer_mobile', 25)->nullable();
            $table->string('product_name_snapshot', 160);
            $table->unsignedInteger('quantity');
            $table->unsignedBigInteger('unit_cost_paise');
            $table->unsignedBigInteger('unit_sale_paise');
            $table->unsignedBigInteger('total_cost_paise');
            $table->unsignedBigInteger('total_sale_paise');
            $table->string('status', 32)->default('design_pending');
            $table->text('design_instructions')->nullable();
            $table->text('approval_note')->nullable();
            $table->timestamp('design_approved_at')->nullable();
            $table->foreignId('design_approved_recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->date('due_date')->nullable();
            $table->timestamps();
            $table->index(['business_id', 'branch_id', 'status'], 'subl_order_business_branch_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sublimation_orders');
        Schema::dropIfExists('sublimation_products');
    }
};
