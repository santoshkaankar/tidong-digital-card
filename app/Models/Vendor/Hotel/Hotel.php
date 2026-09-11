<?php

namespace App\Models\Vendor\Hotel;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    use HasFactory;

    protected $table = 'hotels';

    protected $fillable = [
        'user_id',
        'hotel_name',
        'hotel_slug',
        'category',
        'address',
        'contact_number',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}