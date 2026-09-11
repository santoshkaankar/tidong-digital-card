<?php

namespace App\Models\Vendor\Emporium;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Emporium extends Model
{
    use HasFactory;

    protected $table = 'emporiums';

    protected $fillable = [
        'user_id',
        'store_name',
        'store_slug',
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