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
        Schema::create('user_card_views', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->index('user_card_views_user_id_foreign');
            $table->unsignedBigInteger('visiting_card_id')->index('user_card_views_visiting_card_id_foreign');
            $table->string('card_slug')->unique();
            $table->string('theme_style')->default('diary');
            $table->string('theme_category_code', 5)->default('A');
            $table->integer('variant_number')->default(1);
            $table->string('full_card_no', 60)->nullable()->unique();
            $table->string('font_family')->nullable();
            $table->string('icon_style')->nullable();
            $table->string('icon_display_mode')->nullable();
            $table->string('custom_text_color')->nullable();
            $table->string('custom_icon_color')->nullable();
            $table->json('field_toggles')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_card_views');
    }
};
