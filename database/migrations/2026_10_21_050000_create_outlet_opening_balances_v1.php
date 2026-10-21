<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('outlet_opening_states', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses')->restrictOnDelete();
            $table->foreignId('branch_id')->unique()->constrained('branches')->restrictOnDelete();
            $table->date('opening_date');
            $table->boolean('is_locked')->default(false);
            $table->unsignedInteger('lock_version')->default(0);
            $table->foreignId('locked_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->timestamp('locked_at')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });
        Schema::create('outlet_ledger_opening_lines', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->restrictOnDelete();
            $table->foreignId('ledger_id')->unique()->constrained('finance_accounts')->restrictOnDelete();
            $table->string('direction', 6)->default('debit');
            $table->unsignedBigInteger('amount_paise')->default(0);
            $table->foreignId('updated_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
            $table->index(['branch_id', 'ledger_id']);
        });
        Schema::create('outlet_stock_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses')->restrictOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->restrictOnDelete();
            $table->string('sku', 80);
            $table->string('name', 180);
            $table->string('unit', 20);
            $table->decimal('opening_quantity', 19, 3)->default(0);
            $table->decimal('quantity_on_hand', 19, 3)->default(0);
            $table->unsignedBigInteger('opening_unit_cost_paise')->default(0);
            $table->unsignedBigInteger('opening_stock_value_paise')->default(0);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
            $table->unique(['branch_id', 'sku']);
            $table->index(['business_id', 'branch_id', 'is_active']);
        });
        Schema::create('outlet_stock_movements', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses')->restrictOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->restrictOnDelete();
            $table->foreignId('stock_item_id')->constrained('outlet_stock_items')->restrictOnDelete();
            $table->string('movement_type', 30);
            $table->decimal('quantity_delta', 19, 3);
            $table->decimal('quantity_after', 19, 3);
            $table->string('reference_type', 40);
            $table->string('reference_key', 100);
            $table->string('reason', 500);
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamp('created_at')->useCurrent();
            $table->unique(['stock_item_id', 'reference_type', 'reference_key'], 'outlet_stock_movement_idempotency');
            $table->index(['branch_id', 'created_at']);
        });
        Schema::create('outlet_opening_audit_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses')->restrictOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->restrictOnDelete();
            $table->foreignId('actor_user_id')->constrained('users')->restrictOnDelete();
            $table->string('action', 75);
            $table->string('entity_type', 60);
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('reason', 500);
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['branch_id', 'created_at']);
        });
    }

    public function down(): void
    {
        throw new \RuntimeException('Financial openings, inventory and audit history cannot be automatically dropped.');
    }
};
