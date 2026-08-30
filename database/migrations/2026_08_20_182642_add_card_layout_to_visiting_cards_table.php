<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('visiting_cards', function (Blueprint $table) {
            if (!Schema::hasColumn('visiting_cards', 'card_layout')) {
                $table->string('card_layout')->default('layout_icons')->after('design_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('visiting_cards', function (Blueprint $table) {
            $table->dropColumn('card_layout');
        });
    }
};