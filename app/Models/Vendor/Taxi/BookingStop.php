<?php

namespace App\Models\Vendor;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingStop extends Model
{
    use HasFactory;

    protected $fillable = [
        'taxi_booking_id',
        'tourist_spot_id',
        'stop_order'
    ];

    public function spot()
    {
        return $this->belongsTo(TouristSpot::class, 'tourist_spot_id');
    }

    public function booking()
    {
        return $this->belongsTo(TaxiBooking::class, 'taxi_booking_id');
    }
}