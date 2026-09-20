<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Purani item_categories table me naye fields add karna (bina data delete kiye)
        Schema::table('item_categories', function (Blueprint $table) {
            if (!Schema::hasColumn('item_categories', 'parent_id')) {
                $table->unsignedBigInteger('parent_id')->nullable();
            }
            if (!Schema::hasColumn('item_categories', 'level')) {
                $table->tinyInteger('level')->default(1);
            }
            if (!Schema::hasColumn('item_categories', 'status')) {
                $table->string('status')->default('active');
            }
        });

        // Purani global_items table me naye fields add karna (bina data delete kiye)
        Schema::table('global_items', function (Blueprint $table) {
            if (!Schema::hasColumn('global_items', 'sub_category_id')) {
                $table->unsignedBigInteger('sub_category_id')->nullable();
            }
            if (!Schema::hasColumn('global_items', 'child_category_id')) {
                $table->unsignedBigInteger('child_category_id')->nullable();
            }
            if (!Schema::hasColumn('global_items', 'brand_name')) {
                $table->string('brand_name')->nullable();
            }
            if (!Schema::hasColumn('global_items', 'product_summary')) {
                $table->text('product_summary')->nullable();
            }
            if (!Schema::hasColumn('global_items', 'sku')) {
                $table->string('sku')->nullable();
            }
            if (!Schema::hasColumn('global_items', 'barcode')) {
                $table->string('barcode')->nullable();
            }
            if (!Schema::hasColumn('global_items', 'product_type')) {
                $table->string('product_type')->default('single');
            }
            if (!Schema::hasColumn('global_items', 'min_order_quantity')) {
                $table->integer('min_order_quantity')->default(1);
            }
            if (!Schema::hasColumn('global_items', 'quantity_step_size')) {
                $table->integer('quantity_step_size')->default(1);
            }
            if (!Schema::hasColumn('global_items', 'total_allowed_quantity')) {
                $table->integer('total_allowed_quantity')->default(100);
            }
            if (!Schema::hasColumn('global_items', 'is_cancelable')) {
                $table->boolean('is_cancelable')->default(true);
            }
            if (!Schema::hasColumn('global_items', 'is_returnable')) {
                $table->boolean('is_returnable')->default(true);
            }
            if (!Schema::hasColumn('global_items', 'returnable_days')) {
                $table->integer('returnable_days')->default(7);
            }
            if (!Schema::hasColumn('global_items', 'is_inclusive_tax')) {
                $table->boolean('is_inclusive_tax')->default(false);
            }
            if (!Schema::hasColumn('global_items', 'is_attachment_required')) {
                $table->boolean('is_attachment_required')->default(false);
            }
            if (!Schema::hasColumn('global_items', 'requires_otp')) {
                $table->boolean('requires_otp')->default(false);
            }
            if (!Schema::hasColumn('global_items', 'tags')) {
                $table->text('tags')->nullable();
            }
            if (!Schema::hasColumn('global_items', 'additional_images')) {
                $table->json('additional_images')->nullable();
            }
        });
    }

    public function down()
    {
        // Rollback
    }
};