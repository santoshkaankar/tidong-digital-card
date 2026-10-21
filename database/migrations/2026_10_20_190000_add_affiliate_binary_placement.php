<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('affiliate_settings', function (Blueprint $table): void {
            $table->foreignId('root_member_id')->nullable()->constrained('affiliate_members')->restrictOnDelete();
            $table->foreignId('a_chain_tail_member_id')->nullable()->constrained('affiliate_members')->restrictOnDelete();
        });
        Schema::table('affiliate_members', function (Blueprint $table): void {
            $table->foreignId('placement_parent_member_id')->nullable()
                ->constrained('affiliate_members')->restrictOnDelete();
            $table->char('placement_leg', 1)->nullable();
            $table->unique(['placement_parent_member_id', 'placement_leg'], 'affiliate_parent_leg_unique');
            $table->index(['placement_parent_member_id', 'created_at'], 'affiliate_placement_parent_created');
        });
        // Existing V1 members are NOT re-parented or given a fabricated sponsor.
        // Manual Master reconciliation is required before the root can be initialized.
    }

    public function down(): void
    {
        throw new RuntimeException('Affiliate placement and audit history must not be erased by rollback.');
    }
};
