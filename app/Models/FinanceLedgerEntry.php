<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinanceLedgerEntry extends Model
{
    protected $table = 'finance_ledger_entries';

    public $timestamps = false;

    protected $fillable = [
        'transaction_id',
        'ledger_id',
        'entry_type',
        'amount',
        'currency',
        'balance_after',
        'narration',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:4',
            'balance_after' => 'decimal:4',
            'created_at' => 'datetime',
        ];
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(
            FinanceTransaction::class,
            'transaction_id'
        );
    }

    public function ledger(): BelongsTo
    {
        return $this->belongsTo(
            FinanceAccount::class,
            'ledger_id'
        );
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }
}
