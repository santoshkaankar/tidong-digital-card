<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('sublimation_products', function (Blueprint $table): void {
            $table->json('option_choices')->nullable();
        });
        Schema::table('sublimation_orders', function (Blueprint $table): void {
            $table->json('personalization_options')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('sublimation_orders', fn (Blueprint $table) => $table->dropColumn('personalization_options'));
        Schema::table('sublimation_products', fn (Blueprint $table) => $table->dropColumn('option_choices'));
    }
};
