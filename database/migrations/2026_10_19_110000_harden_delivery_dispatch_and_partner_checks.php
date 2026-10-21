<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Additive V2 hardening. Works even if the original V1 migrations ran already.
        if (Schema::hasTable('delivery_partners')) {
            Schema::table('delivery_partners', function (Blueprint $table): void {
                if (!Schema::hasColumn('delivery_partners', 'verification_status')) $table->string('verification_status', 20)->default('unverified');
                if (!Schema::hasColumn('delivery_partners', 'verified_by')) $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
                if (!Schema::hasColumn('delivery_partners', 'verified_at')) $table->timestamp('verified_at')->nullable();
                if (!Schema::hasColumn('delivery_partners', 'is_available')) $table->boolean('is_available')->default(false);
            });
        }
        if (Schema::hasTable('delivery_jobs')) {
            Schema::table('delivery_jobs', function (Blueprint $table): void {
                if (!Schema::hasColumn('delivery_jobs', 'handoff_pin_hash')) $table->string('handoff_pin_hash')->nullable();
                if (!Schema::hasColumn('delivery_jobs', 'handoff_verified_at')) $table->timestamp('handoff_verified_at')->nullable();
                if (!Schema::hasColumn('delivery_jobs', 'failure_reason')) $table->string('failure_reason', 500)->nullable();
                if (!Schema::hasColumn('delivery_jobs', 'proof_recipient_name')) $table->string('proof_recipient_name', 160)->nullable();
                if (!Schema::hasColumn('delivery_jobs', 'return_confirmed_at')) $table->timestamp('return_confirmed_at')->nullable();
                if (!Schema::hasColumn('delivery_jobs', 'return_confirmed_by')) $table->foreignId('return_confirmed_by')->nullable()->constrained('users')->nullOnDelete();
                if (!Schema::hasColumn('delivery_jobs', 'delivery_fee_paise')) $table->unsignedBigInteger('delivery_fee_paise')->default(0);
                if (!Schema::hasColumn('delivery_jobs', 'partner_earning_paise')) $table->unsignedBigInteger('partner_earning_paise')->default(0);
            });
        }
    }

    public function down(): void
    {
        throw new \RuntimeException('Delivery and financial snapshots are retained. Automatic destructive rollback is blocked.');
    }
};
