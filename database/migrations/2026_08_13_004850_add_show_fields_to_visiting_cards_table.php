<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('visiting_cards', function (Blueprint $table) {
            $table->boolean('show_business')->default(1);
            $table->boolean('show_tagline')->default(1);
            $table->boolean('show_phone')->default(1);
            $table->boolean('show_alt_phone')->default(1);
            $table->boolean('show_whatsapp')->default(1);
            $table->boolean('show_gmail')->default(1);
            $table->boolean('show_yahoo_email')->default(1);
            $table->boolean('show_other_email')->default(1);
            $table->boolean('show_facebook')->default(1);
            $table->boolean('show_instagram')->default(1);
            $table->boolean('show_twitter_x')->default(1);
            $table->boolean('show_linkedin')->default(1);
            $table->boolean('show_youtube')->default(1);
            $table->boolean('show_telegram')->default(1);
            $table->boolean('show_website')->default(1);
            $table->boolean('show_phonepe')->default(1);
            $table->boolean('show_gpay')->default(1);
            $table->boolean('show_paytm')->default(1);
            $table->boolean('show_upi')->default(1);
            $table->boolean('show_about_us')->default(1);
            $table->boolean('show_services')->default(1);
            $table->boolean('show_photo')->default(1);
            $table->boolean('show_qr_code')->default(1);
            $table->boolean('show_address')->default(1);
            $table->boolean('show_map')->default(1);
        });
    }

    public function down(): void
    {
        Schema::table('visiting_cards', function (Blueprint $table) {
            $table->dropColumn([
                'show_business', 'show_tagline', 'show_phone', 'show_alt_phone', 
                'show_whatsapp', 'show_gmail', 'show_yahoo_email', 'show_other_email', 
                'show_facebook', 'show_instagram', 'show_twitter_x', 'show_linkedin', 
                'show_youtube', 'show_telegram', 'show_website', 'show_phonepe', 
                'show_gpay', 'show_paytm', 'show_upi', 'show_about_us', 
                'show_services', 'show_photo', 'show_qr_code', 'show_address', 'show_map'
            ]);
        });
    }
};