<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitingCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'card_no', 'user_id', 'card_type', 'design_type', 'plan_type',
        'name', 'nickname', 'business_name', 'designation', 'tagline', 'owner_name',
        'phone', 'alt_phone', 'whatsapp',
        'gmail', 'yahoo_email', 'other_email',
        'facebook', 'instagram', 'twitter_x', 'linkedin', 'youtube', 'telegram', 'website_link', 'map_location_link',
        'phonepe', 'gpay', 'paytm', 'upi_id',
        'about_us', 'services_or_products', 'catalog_pdf',
        'photo', 'qr_code', 'address', 'city', 'state', 'pincode', 'area',
        'show_business', 'show_tagline', 'show_phone', 'show_alt_phone', 'show_whatsapp',
        'show_gmail', 'show_yahoo_email', 'show_other_email', 'show_facebook', 'show_instagram',
        'show_twitter_x', 'show_linkedin', 'show_youtube', 'show_telegram', 'show_website',
        'show_phonepe', 'show_gpay', 'show_paytm', 'show_upi', 'show_about_us',
        'show_services', 'show_photo', 'show_qr_code', 'show_address', 'show_map'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}