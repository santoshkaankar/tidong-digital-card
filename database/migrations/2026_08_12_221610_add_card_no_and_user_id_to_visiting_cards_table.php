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
            $table->string('card_no')->unique()->after('id');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade')->after('card_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visiting_cards', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['card_no', 'user_id']);
        });
    }
};