<?php

namespace App\Models\Vendor\Taxi;

use Illuminate\Database\Eloquent\Model;

class TaxiBooking extends Model
{
    protected $guarded = [];

    public function taxi()
    {
        return $this->belongsTo(Taxi::class, 'taxi_id');
    }

    public function stops()
    {
        return $this->hasMany(BookingStop::class, 'booking_id');
    }
}