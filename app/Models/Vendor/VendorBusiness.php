<?php

namespace App\Models\Vendor;

use Illuminate\Database\Eloquent\Model;

class VendorBusiness extends Model
{
    protected $table = 'vendor_businesses';

    protected $fillable = [
        'user_id',
        'business_name',
        'business_type',
        'slug',
        'phone',
        'address',
        'city',
        'pincode',
        'is_active',
    ];

    // Relation to User
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}