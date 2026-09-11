<?php

namespace App\Models\Vendor;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Taxi extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'vehicle_name',
        'vehicle_number',
        'vehicle_type',
        'passenger_capacity',
        'luggage_capacity',
        'rate_per_km',
        'base_fare',
        'night_charge',
        'image',
        'status'
    ];

    public function vendor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function bookings()
    {
        return $this->hasMany(TaxiBooking::class);
    }
}