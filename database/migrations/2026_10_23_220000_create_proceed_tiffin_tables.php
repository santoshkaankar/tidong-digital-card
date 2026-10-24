<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('proceed_tiffin_items',function(Blueprint $t){
            $t->id(); $t->foreignId('branch_id')->constrained('branches')->restrictOnDelete();
            $t->foreignId('global_menu_item_id')->nullable()->constrained('global_menu_items')->restrictOnDelete();
            $t->string('name',150); $t->unsignedBigInteger('mrp_paise'); $t->unsignedBigInteger('sale_price_paise');
            $t->boolean('is_active')->default(true); $t->timestamps();
            $t->unique(['branch_id','global_menu_item_id'],'ptf_branch_global_unique');
        });
        Schema::create('proceed_tiffin_meals',function(Blueprint $t){
            $t->id(); $t->foreignId('branch_id')->constrained('branches')->restrictOnDelete();
            $t->string('schedule_key',48); // day:YYYY-MM-DD:slot or everyday:slot
            $t->string('schedule_scope',12); $t->date('service_date')->nullable();
            $t->string('meal_type',24); $t->string('name',150);
            $t->unsignedBigInteger('mrp_total_paise'); $t->unsignedBigInteger('sale_price_paise');
            $t->string('status',20)->default('draft'); $t->timestamp('published_at')->nullable(); $t->timestamps();
            $t->unique(['branch_id','schedule_key'],'ptf_meal_slot_unique');
            $t->index(['branch_id','schedule_scope','service_date','status'],'ptf_meal_scope_idx');
        });
        Schema::create('proceed_tiffin_meal_items',function(Blueprint $t){
            $t->id(); $t->foreignId('meal_id')->constrained('proceed_tiffin_meals')->restrictOnDelete();
            $t->foreignId('item_id')->constrained('proceed_tiffin_items')->restrictOnDelete();
            $t->string('name_snapshot',150); $t->unsignedInteger('quantity');
            $t->unsignedBigInteger('unit_mrp_paise'); $t->unsignedBigInteger('line_total_paise'); $t->timestamps();
            $t->unique(['meal_id','item_id'],'ptf_meal_item_unique');
        });
        Schema::create('proceed_tiffin_orders',function(Blueprint $t){
            $t->id(); $t->string('order_number',40)->unique();
            $t->foreignId('branch_id')->constrained('branches')->restrictOnDelete();
            $t->foreignId('meal_id')->constrained('proceed_tiffin_meals')->restrictOnDelete();
            $t->foreignId('created_by_user_id')->constrained('users')->restrictOnDelete();
            $t->string('meal_name_snapshot',150); $t->string('schedule_scope',12);
            $t->date('service_date'); $t->unsignedInteger('quantity');
            $t->unsignedBigInteger('unit_price_paise'); $t->unsignedBigInteger('total_paise');
            $t->string('customer_name',100)->nullable(); $t->string('status',20)->default('received');
            $t->string('payment_status',20)->default('unpaid'); $t->timestamp('placed_at'); $t->timestamps();
            $t->index(['branch_id','service_date','status'],'ptf_order_idx');
        });
    }
    public function down(): void {
        throw new RuntimeException('Proceed Tiffin historical orders require explicit retention review. Rollback disabled.');
    }
};