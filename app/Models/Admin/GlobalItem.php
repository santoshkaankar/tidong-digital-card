<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlobalItem extends Model
{
    use HasFactory;

    protected $table = 'global_items';

    protected $fillable = [
        'category',
        'food_type',
        'item_name',
        'item_pic',
        'mrp',
        'default_price',
        'description',
        'status',
        'requested_by',
        'is_approved',
    ];
}