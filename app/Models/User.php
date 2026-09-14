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
        'username', 
        'mobile', 
        'google_id', 
        'avatar',
        'vehicle_no',
        'license_no',
        'categories',
        'referral_id', // MLM Referral ID
        'sponsor_id',  // MLM Sponsor ID
        'parent_id',   // MLM Binary Parent ID
        'position',    // MLM Binary Position (left/right)
        'slug',        // URL slug
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