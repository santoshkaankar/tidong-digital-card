<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FinanceAccount extends Model
{
    use SoftDeletes;

    protected $table = 'finance_accounts';

    protected $fillable = [
        'ledger_code',
        'ledger_name',
        'ledger_group_id',
        'currency',
        'account_type',
        'owner_type',
        'owner_id',
        'opening_balance',
        'current_balance',
        'status',
        'description',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'opening_balance' => 'decimal:4',
            'current_balance' => 'decimal:4',
            'metadata' => 'array',
        ];
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(
            FinanceLedgerGroup::class,
            'ledger_group_id'
        );
    }

    public function entries(): HasMany
    {
        return $this->hasMany(
            FinanceLedgerEntry::class,
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
