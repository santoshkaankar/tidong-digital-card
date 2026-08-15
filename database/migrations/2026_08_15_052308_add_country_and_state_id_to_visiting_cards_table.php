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
        $table->unsignedBigInteger('country_id')->nullable()->after('design_type');
        $table->unsignedBigInteger('state_id')->nullable()->after('country_id');
    });
}

public function down(): void
{
    Schema::table('visiting_cards', function (Blueprint $table) {
        $table->dropColumn(['country_id', 'state_id']);
    });
}
};
