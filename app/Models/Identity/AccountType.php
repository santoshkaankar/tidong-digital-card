<?php

namespace App\Models\Identity;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AccountType extends Model
{
    use HasFactory;

    protected $table = 'account_types';

    protected $fillable = [
        'code',
        'name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'user_account_types',
            'account_type_id',
            'user_id'
        )->withPivot([
            'status',
            'activated_at',
        ])->withTimestamps();
    }

    public function userAccountTypes(): HasMany
    {
        return $this->hasMany(UserAccountType::class, 'account_type_id');
    }
}
