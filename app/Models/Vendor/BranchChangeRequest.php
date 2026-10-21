<?php
namespace App\Models\Vendor;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BranchChangeRequest extends Model
{
    protected $table = 'branch_change_requests';
    protected $fillable = ['branch_id', 'vendor_id', 'change_type', 'branch_license_id', 'old_values', 'proposed_values', 'status', 'requested_by', 'reviewed_by', 'review_reason', 'reviewed_at'];
    protected $casts = ['old_values' => 'array', 'proposed_values' => 'array', 'reviewed_at' => 'datetime'];
    public function branch(): BelongsTo { return $this->belongsTo(Branch::class); }
    public function vendor(): BelongsTo { return $this->belongsTo(Vendor::class); }
    public function license(): BelongsTo { return $this->belongsTo(BranchLicense::class, 'branch_license_id'); }
}
