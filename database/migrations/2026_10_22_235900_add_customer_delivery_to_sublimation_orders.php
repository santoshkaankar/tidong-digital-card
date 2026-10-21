<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sublimation_orders', function (Blueprint $table): void {
            $table->foreignId('customer_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('fulfillment_method', 20)->default('pickup');
            $table->text('delivery_address')->nullable();
            $table->string('delivery_carrier', 120)->nullable();
            $table->string('delivery_tracking', 120)->nullable();
            $table->timestamp('dispatched_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->text('delivery_confirmation_note')->nullable();
            $table->index(['customer_user_id', 'created_at'], 'subl_orders_customer_created_idx');
        });
    }

    public function down(): void
    {
        Schema::table('sublimation_orders', function (Blueprint $table): void {
            $table->dropForeign(['customer_user_id']);
            $table->dropIndex('subl_orders_customer_created_idx');
            $table->dropColumn([
                'customer_user_id', 'fulfillment_method', 'delivery_address',
                'delivery_carrier', 'delivery_tracking', 'dispatched_at',
                'delivered_at', 'delivery_confirmation_note',
            ]);
        });
    }
};
