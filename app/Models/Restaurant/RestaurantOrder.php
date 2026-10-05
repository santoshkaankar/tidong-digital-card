<?php

namespace App\Models\Restaurant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class RestaurantOrder extends Model
{
    use HasFactory;

    protected $table = 'restaurant_orders';
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        // Naya order bante hi automatic 4-digit delivery_otp save hoga
        static::creating(function ($order) {
            if (empty($order->delivery_otp)) {
                $order->delivery_otp = str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function table()
    {
        return $this->belongsTo(RestaurantTable::class, 'table_id');
    }

    public function items()
    {
        return $this->hasMany(RestaurantOrderItem::class, 'order_id');
    }

    public function restaurant()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function vendor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    // Delivery Boy Relationship
    public function deliveryBoy()
    {
        return $this->belongsTo(User::class, 'delivery_boy_id');
    }
}