<?php

namespace App\Models\Restaurant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestaurantTable extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'table_number', 
        'seating_capacity', 
        'qr_code_token', 
        'status',
        'selected_items' // Added missing fillable column
    ];

    /**
     * Array ko automatic JSON mein convert aur decode karega
     */
    protected $casts = [
        'selected_items' => 'array',
    ];

    public function orders()
    {
        return $this->hasMany(RestaurantOrder::class, 'table_id');
    }

    public function activeOrder()
    {
        return $this->hasOne(RestaurantOrder::class, 'table_id')->where('status', '!=', 'completed')->where('status', '!=', 'cancelled');
    }
}