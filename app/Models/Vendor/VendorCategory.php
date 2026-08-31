<?php

namespace App\Models\Vendor;

use Illuminate\Database\Eloquent\Model;

class VendorCategory extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'time_slot', // time_slot support
        'description'
    ];
}