<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::table('global_menu_items',function(Blueprint $t){
            $t->unsignedBigInteger('mrp_paise')->nullable();
            $t->unsignedBigInteger('sale_price_paise')->nullable();
        });
    }
    public function down(): void {
        // Existing global items remain safe; never silently delete populated prices.
        throw new RuntimeException('Global master prices must be retained. Automatic rollback disabled.');
    }
};