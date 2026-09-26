<?php

namespace App\Models\Restaurant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestaurantOrder extends Model
{
    use HasFactory;

    protected $table = 'restaurant_orders';

    protected $fillable = [
        'user_id',
        'customer_id',
        'table_id',
        'order_number',
        'order_type',
        'is_guest',
        'guest_session_id',
        'device_ip',
        'customer_name',
        'customer_phone',
        'sub_total',
        'discount_amount',
        'tax_amount',
        'tip_amount',
        'total_amount',
        'currency_code',
        'exchange_rate',
        'converted_amount',
        'status',
        'payment_status',
        'payment_method',
        'payment_request_status',
        'transaction_id',
        'payment_proof',
        'notes',
        'completed_at',
        'delivery_boy_id',
        'status',
        'delivered_at',
    ];

    public function table()
    {
        return $this->belongsTo(RestaurantTable::class, 'table_id');
    }

    public function items()
    {
        return $this->hasMany(RestaurantOrderItem::class, 'order_id');
    }
}