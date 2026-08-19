<?php

namespace App\Models\Vendor;

use Illuminate\Database\Eloquent\Model;

class GlobalItem extends Model
{
    protected $table = 'global_items';

    protected $fillable = [
        'category',
        'item_name',
        'item_pic',
        'mrp',
        'default_price', // Yeh line zaroori hai
        'description',
        'status',
    ];
}