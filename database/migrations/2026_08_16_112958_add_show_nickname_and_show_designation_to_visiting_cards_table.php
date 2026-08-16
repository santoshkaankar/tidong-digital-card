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
        $table->tinyInteger('show_nickname')->default(1)->after('nickname');
        $table->tinyInteger('show_designation')->default(1)->after('designation');
    });
}

public function down()
{
    Schema::table('visiting_cards', function (Blueprint $table) {
        $table->dropColumn(['show_nickname', 'show_designation']);
    });
}
};
