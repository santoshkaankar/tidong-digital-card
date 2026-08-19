<?php

namespace App\Models\Vendor;

use Illuminate\Database\Eloquent\Model;

class VendorItem extends Model
{
    protected $fillable = [
        'user_id', 
        'category', 
        'item_name', 
        'description', 
        'price',      // <-- Yahan 'price' add kar diya hai
        'mrp',
        'sale_price', 
        'status'
    ];
}