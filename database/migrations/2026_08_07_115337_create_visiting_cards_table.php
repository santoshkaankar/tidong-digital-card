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
            $table->id();
            
            // 1. Basic & Personal Details
            $table->string('name');
            $table->string('business_name');
            $table->string('tagline')->nullable();
            $table->string('owner_name')->nullable();
            
            // 2. Contact Numbers
            $table->string('phone');
            $table->string('alt_phone')->nullable();
            $table->string('whatsapp')->nullable();
            
            // 3. Emails
            $table->string('gmail')->nullable();
            $table->string('yahoo_email')->nullable();
            $table->string('other_email')->nullable();
            
            // 4. Social Media & Web Links
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('twitter_x')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('youtube')->nullable();
            $table->string('telegram')->nullable();
            $table->string('website_link')->nullable();
            $table->string('map_location_link')->nullable();
            
            // 5. Payment Apps & UPI
            $table->string('phonepe')->nullable();
            $table->string('gpay')->nullable();
            $table->string('paytm')->nullable();
            $table->string('upi_id')->nullable();
            
            // 6. Catalog / Brochure & General Store Special Fields
            $table->text('about_us')->nullable();
            $table->text('services_or_products')->nullable();
            $table->string('catalog_pdf')->nullable();
            
            // 7. Media Files (Photos, Logos, QR)
            $table->string('photo')->nullable();
            $table->string('qr_code')->nullable();
            
            // 8. Address, City, State & Pincode
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('pincode')->nullable();
            
            $table->timestamps();
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