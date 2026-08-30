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
            $table->string('font_family')->nullable()->after('full_card_no');
            $table->string('icon_style')->nullable()->after('font_family');
            $table->string('icon_display_mode')->nullable()->after('icon_style');
            $table->string('custom_text_color')->nullable()->after('icon_display_mode');
            $table->string('custom_icon_color')->nullable()->after('custom_text_color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_card_views', function (Blueprint $table) {
            $table->dropColumn([
                'font_family',
                'icon_style',
                'icon_display_mode',
                'custom_text_color',
                'custom_icon_color',
            ]);
        });
    }
};