<?php

namespace App\Models\Restaurant;

use Illuminate\Database\Eloquent\Model;

class RestaurantCategory extends Model
{
    protected $fillable = ['user_id', 'name', 'slug', 'status'];

    public function items()
    {
        // 'category_id' ki jagah sahi foreign key 'restaurant_category_id' set ki gayi hai
        return $this->hasMany(RestaurantItem::class, 'restaurant_category_id');
    }
}