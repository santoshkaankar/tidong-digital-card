<?php

namespace App\Models\Vendor;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BranchLicense extends Model
{
    protected $table = 'branch_licenses';

    protected $fillable = [
        'branch_id', 'license_type', 'license_number', 'registered_name',
        'issuing_authority', 'issued_on', 'expires_on', 'notes', 'recorded_by_user_id',
    ];

    protected $casts = ['issued_on' => 'date', 'expires_on' => 'date'];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
