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
        Schema::table('visiting_cards', function (Blueprint $table) {
            $table->string('font_family')->default('Inter');
            $table->string('icon_style')->default('circle');
            $table->string('icon_color')->nullable();
            $table->string('text_color')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visiting_cards', function (Blueprint $table) {
            $table->dropColumn([
                'font_family',
                'icon_style',
                'icon_color',
                'text_color'
            ]);
        });
    }
};