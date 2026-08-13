<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('visiting_cards', function (Blueprint $table) {
            // card_type me hum 'atm', 'modern', 'golden', 'silver', 'premium' kuch bhi store kar payenge
            $table->string('card_type')->default('atm')->after('id');
            // plan_type se pata chalega ki free service hai ya paid/premium
            $table->string('plan_type')->default('free')->after('card_type'); 
        });
    }

    public function down()
    {
        Schema::table('visiting_cards', function (Blueprint $table) {
            $table->dropColumn(['card_type', 'plan_type']);
        });
    }
};