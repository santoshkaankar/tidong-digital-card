<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('orders', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('delivery_boy_id')->nullable();
        $table->unsignedBigInteger('vendor_id')->nullable();
        $table->string('business_type')->nullable(); // restaurant, grocery, etc.
        $table->string('status')->default('pending');
        $table->decimal('delivery_fee', 8, 2)->default(0.00);
        $table->decimal('total_amount', 10, 2)->default(0.00);
        $table->string('customer_name')->nullable();
        $table->text('delivery_address')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
