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
    Schema::table('global_items', function (Blueprint $table) {
        // अगर requested_by कॉलम नहीं है तभी जोड़ें
        if (!Schema::hasColumn('global_items', 'requested_by')) {
            $table->unsignedBigInteger('requested_by')->nullable()->after('id');
        }
        
        // अगर is_approved कॉलम नहीं है तभी जोड़ें
        if (!Schema::hasColumn('global_items', 'is_approved')) {
            $table->tinyInteger('is_approved')->default(1)->after('requested_by');
        }
    });
}

public function down()
{
    Schema::table('global_items', function (Blueprint $table) {
        $table->dropColumn(['requested_by', 'is_approved']);
    });
}
};
