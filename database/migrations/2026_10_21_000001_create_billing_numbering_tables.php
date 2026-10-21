<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('billing_number_counters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_id');
            $table->unsignedBigInteger('outlet_id');
            $table->string('series_type', 3); // GL or 2-letter voucher code
            $table->unsignedInteger('book_size')->default(50);
            $table->boolean('reset_each_fy')->default(true);
            $table->unsignedBigInteger('last_serial')->default(0);
            $table->unsignedBigInteger('last_book')->default(0);
            $table->unsignedInteger('used_in_book')->default(0);
            $table->string('last_fy', 5)->nullable();
            $table->timestamps();
            $table->unique(['business_id', 'outlet_id', 'series_type'], 'bnc_scope_type_uq');
        });

        Schema::create('billing_number_allocations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_id');
            $table->unsignedBigInteger('outlet_id');
            $table->uuid('request_key');
            $table->string('voucher_code', 2);
            $table->string('financial_year', 5);
            $table->string('global_number', 64);
            $table->string('type_number', 64);
            $table->unsignedBigInteger('global_serial');
            $table->unsignedBigInteger('type_serial');
            $table->unsignedBigInteger('actor_user_id');
            $table->timestamps();
            $table->unique(['business_id', 'outlet_id', 'request_key'], 'bna_request_uq');
            $table->unique(['business_id', 'outlet_id', 'financial_year', 'global_number'], 'bna_global_uq');
            $table->unique(['business_id', 'outlet_id', 'voucher_code', 'financial_year', 'type_number'], 'bna_type_uq');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billing_number_allocations');
        Schema::dropIfExists('billing_number_counters');
    }
};
