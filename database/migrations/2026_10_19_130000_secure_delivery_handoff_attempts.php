<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Additive PIN brute-force protection; preserves all historical delivery records. */
return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('delivery_jobs')) {
            throw new \RuntimeException('Install and review Delivery V3 migrations before this hardening migration.');
        }
        if (!Schema::hasColumn('delivery_jobs', 'handoff_failed_attempts')) {
            Schema::table('delivery_jobs', function (Blueprint $table): void {
                $table->unsignedSmallInteger('handoff_failed_attempts')->default(0);
            });
        }
        if (!Schema::hasColumn('delivery_jobs', 'handoff_last_failed_at')) {
            Schema::table('delivery_jobs', function (Blueprint $table): void {
                $table->timestamp('handoff_last_failed_at')->nullable();
            });
        }
        if (!Schema::hasColumn('delivery_jobs', 'handoff_locked_until')) {
            Schema::table('delivery_jobs', function (Blueprint $table): void {
                $table->timestamp('handoff_locked_until')->nullable();
            });
        }
        if (!Schema::hasColumn('delivery_jobs', 'handoff_pin_rotated_at')) {
            Schema::table('delivery_jobs', function (Blueprint $table): void {
                $table->timestamp('handoff_pin_rotated_at')->nullable();
            });
        }
    }

    public function down(): void
    {
        throw new \RuntimeException('Delivery PIN security history must not be destructively rolled back.');
    }
};
