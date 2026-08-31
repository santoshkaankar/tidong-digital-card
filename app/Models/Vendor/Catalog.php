<?php

namespace App\Models\Vendor;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Catalog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'address',
        'slug',
        'item_ids',
    ];

    protected $casts = [
        'item_ids' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}