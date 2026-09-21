<?php

namespace App\Models\Restaurant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TiffinCatalog extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'title',
        'delivery_address',
        'is_active',
        'single_day_rate',
        'full_week_rate',
        'full_month_rate',
        'qr_code_token',
    ];

    public function items()
    {
        return $this->hasMany(TiffinCatalogItem::class, 'tiffin_catalog_id');
    }
}