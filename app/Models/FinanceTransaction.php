<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FinanceTransaction extends Model
{
    protected $table = 'finance_transactions';

    protected $fillable = [
        'transaction_ref',
        'idempotency_key',
        'transaction_type',
        'currency',
        'amount',
        'description',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:4',
            'metadata' => 'array',
            'posted_at' => 'datetime',
        ];
    }

    public function entries(): HasMany
    {
        return $this->hasMany(
            FinanceLedgerEntry::class,
            'transaction_id'
        );
    }

    public function reversalOf(): BelongsTo
    {
        return $this->belongsTo(
            FinanceTransaction::class,
            'reverses_transaction_id'
        );
    }

    public function reversals(): HasMany
    {
        return $this->hasMany(
            FinanceTransaction::class,
            'reverses_transaction_id'
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
