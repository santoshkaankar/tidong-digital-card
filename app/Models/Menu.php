<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\VendorItem;

class Menu extends Model
{
    protected $fillable = ['user_id', 'business_name', 'whatsapp_number', 'type'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(MenuItem::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}