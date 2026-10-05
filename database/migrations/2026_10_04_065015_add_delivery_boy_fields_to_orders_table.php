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
    Schema::table('orders', function (Blueprint $table) {
        if (!Schema::hasColumn('orders', 'delivery_boy_id')) {
            $table->foreignId('delivery_boy_id')->nullable()->constrained('users')->onDelete('set null');
        }
        if (!Schema::hasColumn('orders', 'assignment_mode')) {
            $table->enum('assignment_mode', ['manual', 'auto'])->default('manual');
        }
        if (!Schema::hasColumn('orders', 'delivery_status')) {
            $table->string('delivery_status')->default('pending'); // pending, assigned, accepted, picked_up, delivered
        }
    });
}

public function down()
{
    Schema::table('orders', function (Blueprint $table) {
        $table->dropColumn(['delivery_boy_id', 'assignment_mode', 'delivery_status']);
    });
}
};
