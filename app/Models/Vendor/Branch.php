<?php

namespace App\Models\Vendor;

use App\Models\Authorization\EmployeeAssignment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'branches';

    protected $fillable = [
        'business_id',
        'name',
        'branch_code',
        'phone',
        'email',
        'address_line',
        'country_code',
        'state',
        'city',
        'pincode',
        'india_post_office_id',
        'district',
        'post_office',
        'latitude',
        'longitude',
        'status',
        'is_accepting_orders',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'is_accepting_orders' => 'boolean',
    ];

    public function licenses(): HasMany
    {
        return $this->hasMany(BranchLicense::class, 'branch_id');
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    public function employeeAssignments(): HasMany
    {
        return $this->hasMany(
            EmployeeAssignment::class,
            'branch_id'
        );
    }
}
