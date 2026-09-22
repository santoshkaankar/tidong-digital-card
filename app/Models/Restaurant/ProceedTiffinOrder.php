<?php

namespace App\Models\Restaurant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProceedTiffinOrder extends Model
{
    use HasFactory;

    protected $table = 'proceed_tiffin_orders';

    protected $fillable = [
        'vendor_id',
        'customer_name',
        'customer_mobile',
        'duration',
        'from_date',
        'to_date',
        'meal_types',
        'selected_catalogs',
        'status',
    ];

    protected $casts = [
        'meal_types' => 'array',
        'selected_catalogs' => 'array',
    ];
}