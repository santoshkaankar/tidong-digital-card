<?php

namespace App\Models\Vendor;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Business extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'businesses';

    protected $fillable = [
        'vendor_id',
        'business_type',
        'name',
        'slug',
        'status',
        'is_accepting_orders',
        'settings',
    ];

    protected $casts = [
        'is_accepting_orders' => 'boolean',
        'settings' => 'array',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class, 'business_id');
    }
}
