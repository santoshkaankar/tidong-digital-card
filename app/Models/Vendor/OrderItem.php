<?php

namespace App\Models\Vendor;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = ['order_id', 'menu_id', 'item_name', 'quantity', 'price'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}