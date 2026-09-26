<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 
        'email', 
        'password', 
        'role', 
        'status', 
        'business_type', 
        'food_type',       // Veg / Non-Veg / Both
        'username', 
        'mobile', 
        'google_id', 
        'avatar',
        'profile_photo',
        'gender',
        'dob',
        'vehicle_no',
        'license_no',
        'categories',
        'referral_id',
        'sponsor_id',
        'parent_id',
        'position',
        'slug',
        
        // KYC & Business Fields
        'pan_number',
        'pan_image',
        'aadhaar_number',
        'aadhaar_front_image',
        'aadhaar_back_image',
        'kyc_status',
        'gstin',           // GST Registration Number

        // Naye Fields (FSSAI, MCA, Certificates & UPI QR)
        'fssai_number',
        'fssai_certificate',
        'gst_certificate',
        'mca_certificate',
        'upi_qr_code',

        // Bank Details
        'account_holder_name',
        'bank_name',
        'account_number',
        'ifsc_code',
        'upi_id',

        // Address Details
        'address',
        'area',            // Local Area
        'city',
        'state',
        'pincode',
    ];

    protected $hidden = [
        'password', 
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'categories' => 'array',
        ];
    }
}