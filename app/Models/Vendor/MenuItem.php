<?php

namespace App\Models\Vendor;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    protected $fillable = ['menu_id', 'item_name', 'description', 'price', 'image'];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}