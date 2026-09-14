<?php

namespace App\Models\Restaurant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestaurantCustomItem extends Model
{
    use HasFactory;

    protected $table = 'restaurant_custom_items';

    protected $guarded = [];

    // Relationships (optional but recommended)
    public function category()
    {
        return $this->belongsTo(RestaurantCategory::class, 'restaurant_category_id');
    }

    public function tax()
    {
        return $this->belongsTo(\App\Models\Admin\Tax::class, 'tax_id');
    }
}