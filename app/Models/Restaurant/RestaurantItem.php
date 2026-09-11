<?php

namespace App\Models\Restaurant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\GlobalItem;

class RestaurantItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'global_item_id',
        'restaurant_category_id',
        'mrp',
        'price',
        'is_available',
        'status',
    ];

    // Global Item se Details (Name, Type) laane ke liye
    public function globalItem()
    {
        return $this->belongsTo(GlobalItem::class, 'global_item_id');
    }

    // Category Details laane ke liye
    public function category()
    {
        return $this->belongsTo(RestaurantCategory::class, 'restaurant_category_id');
    }
}