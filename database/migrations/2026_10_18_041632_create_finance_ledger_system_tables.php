<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('finance_ledger_groups', function (Blueprint $table) {
            $table->id();
            $table->string('group_code', 40)->unique();
            $table->string('group_name', 150);
            $table->foreignId('parent_group_id')->nullable()->constrained('finance_ledger_groups')->restrictOnDelete();
            $table->string('nature', 30);
            $table->string('currency', 10)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_system')->default(false);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['group_name', 'is_active']);
            $table->index(['nature', 'currency']);
        });

        Schema::create('finance_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('ledger_code', 50)->unique();
            $table->string('ledger_name', 180);
            $table->foreignId('ledger_group_id')->constrained('finance_ledger_groups')->restrictOnDelete();
            $table->string('currency', 10);
            $table->string('account_type', 40);
            $table->string('owner_type', 40)->default('system');
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->decimal('opening_balance', 24, 4)->default(0);
            $table->decimal('current_balance', 24, 4)->default(0);
            $table->string('status', 20)->default('active');
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['ledger_name', 'status']);
            $table->index(['ledger_group_id', 'status']);
            $table->index(['currency', 'status']);
            $table->index(['owner_type', 'owner_id']);
        });
        Schema::create('finance_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_ref', 60)->unique();
            $table->string('idempotency_key', 100)->nullable()->unique();
            $table->string('transaction_type', 40);
            $table->string('currency', 10);
            $table->decimal('amount', 24, 4);
            $table->string('status', 20)->default('draft');
            $table->string('description', 500)->nullable();
            $table->json('metadata')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('reverses_transaction_id')->nullable()->constrained('finance_transactions')->restrictOnDelete();
            $table->timestamp('posted_at')->nullable();
            $table->timestamps();
            $table->index(['currency', 'status', 'created_at']);
            $table->index(['transaction_type', 'created_at']);
        });

        Schema::create('finance_ledger_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('finance_transactions')->restrictOnDelete();
            $table->foreignId('ledger_id')->constrained('finance_accounts')->restrictOnDelete();
            $table->string('entry_type', 10);
            $table->decimal('amount', 24, 4);
            $table->string('currency', 10);
            $table->decimal('balance_after', 24, 4)->nullable();
            $table->string('narration', 500)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['ledger_id', 'created_at']);
            $table->index(['entry_type', 'created_at']);
            $table->index(['currency', 'created_at']);
        });        Schema::create('finance_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 80);
            $table->string('entity_type', 80);
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->text('reason')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->json('metadata')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('request_id', 100)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['entity_type', 'entity_id']);
            $table->index(['actor_user_id', 'created_at']);
            $table->index(['action', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_audit_logs');
        Schema::dropIfExists('finance_ledger_entries');
        Schema::dropIfExists('finance_transactions');
        Schema::dropIfExists('finance_accounts');
        Schema::dropIfExists('finance_ledger_groups');
    }
};