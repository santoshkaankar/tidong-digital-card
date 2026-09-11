<?php

namespace App\Models\Restaurant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Restaurant\RestaurantTable;

class CashCall extends Model
{
    use HasFactory;

    protected $table = 'waiter_calls';

    protected $fillable = [
        'user_id', 
        'table_id', 
        'call_type', 
        'status'
    ];

    public function scopeCashOnly($query)
    {
        return $query->where('call_type', 'pay_bill_cash');
    }

    public function table()
    {
        return $this->belongsTo(RestaurantTable::class, 'table_id');
    }
}