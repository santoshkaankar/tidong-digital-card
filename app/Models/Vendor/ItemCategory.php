<?php

namespace App\Models\Vendor;

use Illuminate\Database\Eloquent\Model;

class ItemCategory extends Model
{
    protected $table = 'item_categories';
    protected $fillable = ['name'];

    public function globalItems()
    {
        return $this->hasMany(GlobalItem::class, 'category', 'name');
    }
}