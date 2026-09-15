<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    use HasFactory;

    protected $table = 'wallet_transactions';

    protected $fillable = [
        'vendor_id',
        'wallet_type', // 'bonus' or 'sales'
        'type',        // 'credit' or 'debit'
        'amount',
        'description',
        'order_id',
        'status',
    ];
}