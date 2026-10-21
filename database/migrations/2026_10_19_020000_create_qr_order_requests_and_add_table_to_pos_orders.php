<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::table('pos_orders',function (Blueprint $table) {
            $table->foreignId('dining_table_id')->nullable()->constrained('restaurant_dining_tables')->restrictOnDelete();
            $table->string('order_origin',24)->default('counter');
        });
        Schema::create('restaurant_qr_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dining_table_id')->constrained('restaurant_dining_tables')->restrictOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->restrictOnDelete();
            $table->uuid('submission_key')->unique();
            $table->string('customer_name',80);
            $table->string('customer_note',500)->nullable();
            $table->string('status',24)->default('pending');
            $table->unsignedBigInteger('quoted_total_paise');
            $table->foreignId('accepted_order_id')->nullable()->constrained('pos_orders')->restrictOnDelete();
            $table->foreignId('decided_by_user_id')->nullable()->constrained('users')->restrictOnDelete();
            $table->timestamp('decided_at')->nullable();
            $table->string('rejection_reason',500)->nullable();
            $table->timestamps();
            $table->index(['branch_id','status','created_at']);
        });
        Schema::create('restaurant_qr_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('restaurant_qr_requests')->restrictOnDelete();
            $table->foreignId('menu_item_id')->constrained('pos_menu_items')->restrictOnDelete();
            $table->string('name_snapshot');
            $table->unsignedInteger('quantity');
            $table->unsignedBigInteger('unit_price_paise');
            $table->unsignedBigInteger('line_total_paise');
            $table->timestamps();
        });
    }
    public function down(): void {
        throw new \RuntimeException('QR order requests and POS transactions must not be dropped automatically.');
    }
};
