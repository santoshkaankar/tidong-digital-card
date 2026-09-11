<?php

namespace App\Models\Restaurant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Restaurant\RestaurantTable;

class WaiterCall extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'table_id', 
        'call_type', 
        'status'
    ];

    public function table()
    {
        return $this->belongsTo(RestaurantTable::class, 'table_id');
    }
}