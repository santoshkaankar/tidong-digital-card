<?php

namespace App\Models\Vendor;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class VendorEmployee extends Authenticatable
{
    use Notifiable;

    protected $table = 'vendor_employees';

    protected $fillable = [
        'vendor_id',
        'business_id',
        'name',
        'email',
        'phone',
        'password',
        'designation',
        'permissions',
        'is_active',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_active'   => 'boolean',
        'password'    => 'hashed',
    ];

    // Relation to Master Vendor (User)
    public function vendor()
    {
        return $this->belongsTo(\App\Models\User::class, 'vendor_id');
    }

    // Relation to Specific Business Outlet
    public function business()
    {
        return $this->belongsTo(VendorBusiness::class, 'business_id');
    }
}