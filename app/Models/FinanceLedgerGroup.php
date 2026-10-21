<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FinanceLedgerGroup extends Model
{
    protected $table = 'finance_ledger_groups';

    protected $fillable = [
        'group_code',
        'group_name',
        'parent_group_id',
        'nature',
        'currency',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_system' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(
            FinanceLedgerGroup::class,
            'parent_group_id'
        );
    }

    public function children(): HasMany
    {
        return $this->hasMany(
            FinanceLedgerGroup::class,
            'parent_group_id'
        );
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(
            FinanceAccount::class,
            'ledger_group_id'
        );
    }
}
