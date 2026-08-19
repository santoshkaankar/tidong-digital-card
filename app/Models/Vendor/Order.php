<?php

namespace App\Models\Vendor;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['menu_id', 'table_or_room', 'status', 'payment_mode', 'payment_status', 'total_amount'];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}