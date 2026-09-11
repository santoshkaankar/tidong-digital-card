<?php

namespace App\Models\Restaurant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestaurantOrderItem extends Model
{
    use HasFactory;

    protected $table = 'restaurant_order_items';

    protected $fillable = [
        'order_id', 
        'item_id', 
        'item_name', 
        'quantity', 
        'price', 
        'tax_amount',
        'subtotal',
        'batch_number', 
        'kitchen_status', 
        'item_notes'
    ];

    public function restaurantItem()
    {
        return $this->belongsTo(RestaurantItem::class, 'item_id');
    }

    public function item()
    {
        return $this->belongsTo(RestaurantItem::class, 'item_id');
    }
}