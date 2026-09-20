<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('global_item_variants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('global_item_id');
            $table->string('sku')->nullable();
            $table->string('barcode')->nullable();
            $table->string('color')->nullable();
            $table->string('size')->nullable();
            $table->string('variant_name')->nullable(); // Jaise: Red - XL
            $table->decimal('mrp', 10, 2)->default(0);
            $table->decimal('price', 10, 2)->default(0);
            $table->string('image')->nullable();
            $table->timestamps();
        });

        // Vendor store ke selected variants ke liye table
        Schema::create('vendor_store_item_variants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vendor_id');
            $table->unsignedBigInteger('global_item_variant_id');
            $table->decimal('mrp', 10, 2)->default(0);
            $table->decimal('price', 10, 2)->default(0);
            $table->integer('stock')->default(0);
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('vendor_store_item_variants');
        Schema::dropIfExists('global_item_variants');
    }
};