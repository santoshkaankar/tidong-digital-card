<?php

namespace App\Models\Restaurant;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class RestaurantStaffUser extends Authenticatable
{
    use Notifiable;

    protected $table = 'restaurant_staff_users';

    protected $fillable = [
    'restaurant_id',
    'name',
    'email',
    'phone',
    'role',
    'password',
    'status',
];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'permissions' => 'array',
    ];
}