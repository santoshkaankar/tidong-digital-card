<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pos_menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->restrictOnDelete();
            $table->string('name');
            $table->unsignedBigInteger('price_paise');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['branch_id', 'is_active']);
        });

        Schema::create('pos_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 50)->unique();
            $table->foreignId('vendor_id')->constrained('vendors')->restrictOnDelete();
            $table->foreignId('business_id')->constrained('businesses')->restrictOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->restrictOnDelete();
            $table->foreignId('created_by_user_id')->constrained('users')->restrictOnDelete();
            $table->string('status', 24)->default('received');
            $table->string('payment_status', 24)->default('unpaid');
            $table->unsignedBigInteger('subtotal_paise');
            $table->unsignedBigInteger('total_paise');
            $table->string('customer_note', 500)->nullable();
            $table->timestamp('placed_at');
            $table->timestamps();
            $table->index(['branch_id', 'status', 'placed_at']);
        });

        Schema::create('pos_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('pos_orders')->restrictOnDelete();
            $table->foreignId('menu_item_id')->constrained('pos_menu_items')->restrictOnDelete();
            $table->string('name_snapshot');
            $table->unsignedInteger('quantity');
            $table->unsignedBigInteger('unit_price_paise');
            $table->unsignedBigInteger('line_total_paise');
            $table->timestamps();
            $table->index('order_id');
        });
    }

    // Retain orders and their immutable price snapshots even if the module is retired.
    // Do not automatically drop POS tables on rollback.
    public function down(): void
    {
        throw new \RuntimeException('POS tables contain historical orders. Automatic rollback is disabled; contact a developer for a data-retention plan.');
    }
};
