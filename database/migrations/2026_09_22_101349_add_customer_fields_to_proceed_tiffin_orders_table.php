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
        if (!Schema::hasColumn('proceed_tiffin_orders', 'customer_name')) {
            $table->string('customer_name')->nullable()->after('vendor_id');
        }
        if (!Schema::hasColumn('proceed_tiffin_orders', 'customer_mobile')) {
            $table->string('customer_mobile')->nullable()->after('customer_name');
        }
    });
}

public function down()
{
    Schema::table('proceed_tiffin_orders', function (Blueprint $table) {
        $table->dropColumn(['customer_name', 'customer_mobile']);
    });
}
};
