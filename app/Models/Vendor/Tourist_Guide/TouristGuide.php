<?php

namespace App\Models\Vendor\Tourist_Guide;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TouristGuide extends Model
{
    use HasFactory;

    protected $table = 'tourist_guides';

    protected $fillable = [
        'user_id',
        'agency_name',
        'agency_slug',
        'license_no',
        'address',
        'contact_number',
        'languages',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}