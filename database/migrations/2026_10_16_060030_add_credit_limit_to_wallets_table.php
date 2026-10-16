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
    Schema::table('wallets', function (Blueprint $table) {
        if (!Schema::hasColumn('wallets', 'credit_limit')) {
            $table->decimal('credit_limit', 15, 2)->default(500000.00)->after('real_balance');
        }
        if (!Schema::hasColumn('wallets', 'credit_used')) {
            $table->decimal('credit_used', 15, 2)->default(0.00)->after('credit_limit');
        }
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
            //
        });
    }
};
