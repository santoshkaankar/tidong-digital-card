<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::table('pos_order_items', function (Blueprint $table) {
            $table->unsignedInteger('ready_quantity')->default(0);
            $table->unsignedInteger('served_quantity')->default(0);
            $table->unsignedInteger('added_quantity')->default(0);
        });
        Schema::create('pos_item_progress_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('pos_orders')->restrictOnDelete();
            $table->foreignId('item_id')->constrained('pos_order_items')->restrictOnDelete();
            $table->foreignId('actor_user_id')->constrained('users')->restrictOnDelete();
            $table->string('action',16);
            $table->unsignedInteger('quantity');
            $table->timestamp('created_at')->useCurrent();
            $table->index(['order_id','id']);
        });
    }
    public function down(): void { throw new \RuntimeException('Retain order item progress and audit history.'); }
};
