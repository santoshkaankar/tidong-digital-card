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
        if (!Schema::hasColumn('visiting_cards', 'profile_photo')) {
            $table->string('profile_photo')->nullable();
        }
        if (!Schema::hasColumn('visiting_cards', 'banner_image')) {
            $table->string('banner_image')->nullable();
        }
    });
}

public function down()
{
    Schema::table('visiting_cards', function (Blueprint $table) {
        $table->dropColumn(['profile_photo', 'banner_image']);
    });
}
};
