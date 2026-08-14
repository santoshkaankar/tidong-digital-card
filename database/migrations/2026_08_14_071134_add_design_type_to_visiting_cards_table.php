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
        $table->string('design_type')->nullable()->after('card_type');
    });
}

public function down(): void
{
    Schema::table('visiting_cards', function (Blueprint $table) {
        $table->dropColumn('design_type');
    });
}
};
