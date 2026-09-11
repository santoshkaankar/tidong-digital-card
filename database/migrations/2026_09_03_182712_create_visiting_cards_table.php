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
        Schema::create('visiting_cards', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('card_type')->default('atm');
            $table->string('design_type')->nullable();
            $table->string('card_layout')->default('layout_icons');
            $table->unsignedBigInteger('country_id')->nullable();
            $table->unsignedBigInteger('state_id')->nullable();
            $table->string('plan_type')->default('free');
            $table->string('card_no')->unique();
            $table->string('name');
            $table->string('nickname')->nullable();
            $table->tinyInteger('show_nickname')->default(1);
            $table->string('business_name')->nullable();
            $table->string('designation')->nullable();
            $table->tinyInteger('show_designation')->default(1);
            $table->string('tagline')->nullable();
            $table->string('owner_name')->nullable();
            $table->string('phone');
            $table->string('alt_phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('gmail')->nullable();
            $table->string('yahoo_email')->nullable();
            $table->string('other_email')->nullable();
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('twitter_x')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('youtube')->nullable();
            $table->string('telegram')->nullable();
            $table->string('website_link')->nullable();
            $table->string('website')->nullable();
            $table->string('map_location_link')->nullable();
            $table->text('location_url')->nullable();
            $table->string('phonepe')->nullable();
            $table->string('gpay')->nullable();
            $table->string('paytm')->nullable();
            $table->string('upi_id')->nullable();
            $table->text('about_us')->nullable();
            $table->text('services_or_products')->nullable();
            $table->text('other_details')->nullable();
            $table->string('catalog_pdf')->nullable();
            $table->string('photo')->nullable();
            $table->string('qr_code')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('pincode')->nullable();
            $table->timestamps();
            $table->string('area')->nullable();
            $table->unsignedBigInteger('user_id')->nullable()->index('visiting_cards_user_id_foreign');
            $table->boolean('show_business')->default(true);
            $table->boolean('show_tagline')->default(true);
            $table->boolean('show_phone')->default(true);
            $table->boolean('show_alt_phone')->default(true);
            $table->boolean('show_whatsapp')->default(true);
            $table->boolean('show_gmail')->default(true);
            $table->boolean('show_yahoo_email')->default(true);
            $table->boolean('show_other_email')->default(true);
            $table->boolean('show_facebook')->default(true);
            $table->boolean('show_instagram')->default(true);
            $table->boolean('show_twitter_x')->default(true);
            $table->boolean('show_linkedin')->default(true);
            $table->boolean('show_youtube')->default(true);
            $table->boolean('show_telegram')->default(true);
            $table->boolean('show_website')->default(true);
            $table->boolean('show_phonepe')->default(true);
            $table->boolean('show_gpay')->default(true);
            $table->boolean('show_paytm')->default(true);
            $table->boolean('show_upi')->default(true);
            $table->boolean('show_about_us')->default(true);
            $table->boolean('show_services')->default(true);
            $table->boolean('show_photo')->default(true);
            $table->boolean('show_qr_code')->default(true);
            $table->boolean('show_address')->default(true);
            $table->boolean('show_area')->default(true);
            $table->boolean('show_pincode')->default(true);
            $table->boolean('show_city')->default(true);
            $table->boolean('show_state')->default(true);
            $table->boolean('show_other_details')->default(true);
            $table->boolean('show_location_url')->default(true);
            $table->boolean('show_map')->default(true);
            $table->string('profile_photo')->nullable();
            $table->string('banner_image')->nullable();
            $table->string('font_family')->default('Inter');
            $table->string('icon_style')->default('circle');
            $table->string('icon_color')->nullable();
            $table->string('text_color')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visiting_cards');
    }
};
