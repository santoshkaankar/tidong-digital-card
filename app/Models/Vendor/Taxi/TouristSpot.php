<?php

namespace App\Models\Vendor;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TouristSpot extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'city',
        'state',
        'description',
        'image',
        'latitude',
        'longitude',
        'avg_visit_duration_mins',
        'entry_fee',
        'status'
    ];
}