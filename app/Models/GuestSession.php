<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuestSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_uuid',
        'ip_address',
        'user_agent',
        'last_active_at',
    ];

    protected $casts = [
        'last_active_at' => 'datetime',
    ];
}