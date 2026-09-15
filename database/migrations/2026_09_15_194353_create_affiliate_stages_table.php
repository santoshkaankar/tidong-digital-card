<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('affiliate_stages', function (Blueprint $table) {
            $table->id();
            $table->integer('stage_no')->unique();
            $table->string('stage_name');
            $table->integer('leg_a_count'); // Leg A required active members
            $table->integer('leg_b_count'); // Leg B required active members
            $table->decimal('incentive_amount', 12, 2); // Gross reward amount
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliate_stages');
    }
};