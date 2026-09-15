<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $table = 'wallets';

    protected $fillable = [
        'user_id',
        'real_balance',
        'non_withdrawable_balance',
        't_coins'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}