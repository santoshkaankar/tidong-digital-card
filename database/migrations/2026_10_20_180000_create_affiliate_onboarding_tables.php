<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('affiliate_settings', function (Blueprint $table): void {
            $table->unsignedTinyInteger('id')->primary();
            $table->boolean('registration_enabled')->default(true);
            $table->boolean('referral_tracking_enabled')->default(true);
            $table->unsignedBigInteger('registration_fee_paise')->default(0);
            $table->boolean('fee_collection_enabled')->default(false);
            $table->unsignedInteger('estimate_rate_bps')->default(0);
            $table->boolean('withdrawals_enabled')->default(false);
            $table->boolean('payouts_enabled')->default(false);
            $table->boolean('wallet_credit_enabled')->default(false);
            $table->string('financial_review_status', 30)->default('pending');
            $table->unsignedInteger('policy_version')->default(1);
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('affiliate_members', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->restrictOnDelete();
            $table->string('referral_code', 16)->unique();
            $table->foreignId('referrer_member_id')->nullable()->constrained('affiliate_members')->restrictOnDelete();
            $table->string('status', 24)->default('active');
            $table->timestamp('joined_at');
            $table->timestamps();
            $table->index(['referrer_member_id', 'joined_at']);
        });

        Schema::create('affiliate_stage_references', function (Blueprint $table): void {
            $table->id();
            $table->unsignedSmallInteger('stage_no')->unique();
            $table->string('name');
            $table->unsignedBigInteger('legacy_leg_a_count');
            $table->unsignedBigInteger('legacy_leg_b_count');
            $table->unsignedBigInteger('legacy_incentive_paise');
            $table->string('status', 32)->default('reference_only');
            $table->timestamps();
        });

        Schema::create('affiliate_estimates', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('member_id')->constrained('affiliate_members')->restrictOnDelete();
            $table->foreignId('referred_member_id')->constrained('affiliate_members')->restrictOnDelete();
            $table->string('source_kind', 32); // Verified order reference only; never joins old wallet.
            $table->string('source_reference', 100);
            $table->string('currency', 3)->default('INR');
            $table->unsignedBigInteger('eligible_sale_paise');
            $table->unsignedInteger('rate_bps');
            $table->unsignedBigInteger('estimated_commission_paise');
            $table->unsignedInteger('policy_version');
            $table->string('status', 32)->default('estimate_only');
            $table->timestamp('verified_sale_at');
            $table->timestamps();
            $table->unique(['source_kind', 'source_reference', 'member_id'], 'affiliate_estimate_idempotent');
        });

        Schema::create('affiliate_audit_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('actor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('member_id')->nullable()->constrained('affiliate_members')->restrictOnDelete();
            $table->string('action', 90);
            $table->json('metadata')->nullable();
            $table->timestamp('created_at');
            $table->index(['member_id', 'created_at']);
        });

        DB::table('affiliate_settings')->insert([
            'id' => 1, 'registration_enabled' => true, 'referral_tracking_enabled' => true,
            'registration_fee_paise' => 0, 'fee_collection_enabled' => false,
            'estimate_rate_bps' => 0, 'withdrawals_enabled' => false,
            'payouts_enabled' => false, 'wallet_credit_enabled' => false,
            'financial_review_status' => 'pending', 'policy_version' => 1,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        // Historical rules are kept for legal review; NEVER used for commission or payouts.
        $legacy = [
            [1, 'Global Distributor', 1, 2, 500],
            [2, 'Global Senior Distributor', 2, 4, 1000],
            [3, 'Global Junior Pearl Distributor', 6, 12, 2000],
            [4, 'Global Assistant Pearl Distributor', 44, 66, 8500],
            [5, 'Global Branch Pearl Distributor', 86, 129, 18000],
            [6, 'Global Pearl Distributor', 176, 264, 28000],
            [7, 'Global Junior Rubey Distributor', 356, 534, 45000],
            [8, 'Global Rubey Distributor', 988, 1482, 87000],
            [9, 'Global Junior Emerald Distributor', 2600, 3900, 120000],
            [10, 'Global Assistant Emerald Distributor', 4500, 6750, 180000],
            [11, 'Global Emerald Distributor', 6800, 10200, 250000],
            [12, 'Global Junior Dimond Distributor', 14500, 21750, 300000],
            [13, 'Global Assistant Dimond Distributor', 22000, 33000, 500000],
            [14, 'Global Dimond Distributor', 38000, 57000, 3000000],
        ];
        foreach ($legacy as [$number, $name, $a, $b, $amount]) {
            DB::table('affiliate_stage_references')->insert([
                'stage_no' => $number, 'name' => $name,
                'legacy_leg_a_count' => $a, 'legacy_leg_b_count' => $b,
                'legacy_incentive_paise' => $amount * 100,
                'status' => 'reference_only', 'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        // Financial estimates, memberships and audit history must never be silently erased.
        throw new RuntimeException('Affiliate historical records cannot be rolled back destructively.');
    }
};
