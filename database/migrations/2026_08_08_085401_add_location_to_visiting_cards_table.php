<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('visiting_cards', function (Blueprint $table) {
        $table->string('area')->nullable(); // Yahan 'area' naam ka column add ho jayega
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visiting_cards', function (Blueprint $table) {
            //
        });
    }
};
