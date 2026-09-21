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
        Schema::table('tiffin_catalog_items', function (Blueprint $table) {
            // Yahan column add kiya ja raha hai
            $table->unsignedBigInteger('restaurant_category_id')->nullable()->after('id');
            
            // Agar aapko foreign key constraint bhi lagani ho toh yeh line uncomment kar sakte hain:
            // $table->foreign('restaurant_category_id')->references('id')->on('restaurant_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tiffin_catalog_items', function (Blueprint $table) {
            // Rollback ke waqt column drop karne ke liye
            // $table->dropForeign(['restaurant_category_id']);
            $table->dropColumn('restaurant_category_id');
        });
    }
};