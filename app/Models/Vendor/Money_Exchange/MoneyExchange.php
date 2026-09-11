<?php

namespace App\Models\Vendor\Money_Exchange;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MoneyExchange extends Model
{
    use HasFactory;

    protected $table = 'money_exchanges';

    protected $fillable = [
        'user_id',
        'exchange_name',
        'exchange_slug',
        'license_number',
        'address',
        'contact_number',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}