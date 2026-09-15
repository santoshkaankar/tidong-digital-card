<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Team Stats Table (Leg A & Leg B - Active, Inactive, Totals)
        Schema::create('user_team_stats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->integer('active_leg_a')->default(0);
            $table->integer('inactive_leg_a')->default(0);
            $table->integer('active_leg_b')->default(0);
            $table->integer('inactive_leg_b')->default(0);
            $table->timestamps();
        });

        // 2. Affiliate Payouts & Deductions Table (Gross, Admin, TDS, Net)
        Schema::create('user_affiliate_payouts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('stage_id');
            $table->decimal('gross_amount', 12, 2);
            $table->decimal('admin_charge', 12, 2)->default(0.00);
            $table->decimal('tds_amount', 12, 2)->default(0.00);
            $table->decimal('net_amount', 12, 2);
            $table->enum('status', ['locked', 'unlocked', 'paid'])->default('locked');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_affiliate_payouts');
        Schema::dropIfExists('user_team_stats');
    }
};