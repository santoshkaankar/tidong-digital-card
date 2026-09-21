<?php

namespace App\Models\Restaurant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TiffinCatalogItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'tiffin_catalog_id',
        'day',
        'meal_type',
        'item_ids',
    ];

    protected $casts = [
        'item_ids' => 'array',
    ];

    public function catalog()
    {
        return $this->belongsTo(TiffinCatalog::class, 'tiffin_catalog_id');
    }
}