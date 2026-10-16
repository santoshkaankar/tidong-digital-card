<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
            if (!Schema::hasColumn('wallets', 'credit_card_status')) {
                $table->string('credit_card_status')->default('waiting')->after('credit_used');
            }
            if (!Schema::hasColumn('wallets', 'credit_card_progress')) {
                $table->integer('credit_card_progress')->default(0)->after('credit_card_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
            $table->dropColumn(['credit_card_status', 'credit_card_progress']);
        });
    }
};