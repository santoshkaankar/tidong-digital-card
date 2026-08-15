<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. visiting_cards Table me Base Card Number (Agar pehle se nahi hai)
        if (!Schema::hasColumn('visiting_cards', 'card_no')) {
            Schema::table('visiting_cards', function (Blueprint $table) {
                $table->string('card_no', 30)->nullable()->unique()->after('user_id');
            });
        }

        // 2. user_card_views Table me Serial Numbering Fields Add Karein
        Schema::table('user_card_views', function (Blueprint $table) {
            if (!Schema::hasColumn('user_card_views', 'theme_category_code')) {
                $table->string('theme_category_code', 5)->default('A')->after('theme_style');
            }
            if (!Schema::hasColumn('user_card_views', 'variant_number')) {
                $table->integer('variant_number')->default(1)->after('theme_category_code');
            }
            if (!Schema::hasColumn('user_card_views', 'full_card_no')) {
                $table->string('full_card_no', 60)->nullable()->unique()->after('variant_number');
            }
        });
    }

    public function down(): void
    {
        Schema::table('visiting_cards', function (Blueprint $table) {
            $table->dropColumn('card_no');
        });

        Schema::table('user_card_views', function (Blueprint $table) {
            $table->dropColumn(['theme_category_code', 'variant_number', 'full_card_no']);
        });
    }
};