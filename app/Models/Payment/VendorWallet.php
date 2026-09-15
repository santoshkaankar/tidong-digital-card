<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorWallet extends Model
{
    use HasFactory;

    protected $table = 'vendor_wallets';

    protected $fillable = [
        'vendor_id',
        'bonus_balance',
        'sales_balance',
    ];

    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class, 'vendor_id', 'vendor_id');
    }
}