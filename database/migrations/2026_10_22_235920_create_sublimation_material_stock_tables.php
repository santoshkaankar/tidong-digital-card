<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sublimation_materials', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses')->restrictOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->restrictOnDelete();
            $table->string('name', 160);
            $table->string('sku', 80)->nullable();
            $table->string('unit', 16);
            $table->decimal('on_hand', 17, 3)->default(0);
            $table->decimal('low_stock_level', 17, 3)->default(0);
            $table->timestamps();
            $table->unique(['business_id','branch_id','name'], 'subl_mat_business_branch_name_uniq');
            $table->index(['business_id','branch_id'], 'subl_mat_business_branch_idx');
        });
        Schema::create('sublimation_material_movements', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses')->restrictOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->restrictOnDelete();
            $table->foreignId('material_id')->constrained('sublimation_materials')->restrictOnDelete();
            $table->foreignId('actor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('direction', 8);
            $table->decimal('quantity', 17, 3);
            $table->decimal('balance_after', 17, 3);
            $table->string('reason', 300);
            $table->timestamp('created_at');
            $table->index(['business_id','branch_id','created_at'], 'subl_move_branch_created_idx');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('sublimation_material_movements');
        Schema::dropIfExists('sublimation_materials');
    }
};
