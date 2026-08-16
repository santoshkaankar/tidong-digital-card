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
        $table->string('nickname')->nullable()->after('name');
        $table->string('designation')->nullable()->after('business_name');
    });
}

public function down()
{
    Schema::table('visiting_cards', function (Blueprint $table) {
        $table->dropColumn(['nickname', 'designation']);
    });
}
};
