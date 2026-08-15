<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_card_views', function (Blueprint $table) {
            if (!Schema::hasColumn('user_card_views', 'theme_category_code')) {
                $table->string('theme_category_code', 10)->default('A')->after('theme_style');
            }
            if (!Schema::hasColumn('user_card_views', 'variant_number')) {
                $table->integer('variant_number')->default(1)->after('theme_category_code');
            }
            if (!Schema::hasColumn('user_card_views', 'full_card_no')) {
                $table->string('full_card_no', 60)->nullable()->after('variant_number');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_card_views', function (Blueprint $table) {
            $table->dropColumn(['theme_category_code', 'variant_number', 'full_card_no']);
        });
    }
};