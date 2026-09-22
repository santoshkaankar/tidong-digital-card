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
    Schema::table('proceed_tiffin_orders', function (Blueprint $table) {
        if (!Schema::hasColumn('proceed_tiffin_orders', 'status')) {
            $table->string('status')->default('pending')->after('selected_catalogs');
        }
    });
}

public function down()
{
    Schema::table('proceed_tiffin_orders', function (Blueprint $table) {
        $table->dropColumn('status');
    });
}
};
