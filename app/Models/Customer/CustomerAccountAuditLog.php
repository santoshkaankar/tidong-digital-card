<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Model;

class CustomerAccountAuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id', 'event_code', 'target_id', 'ip_address', 'created_at'];

    protected $casts = ['created_at' => 'datetime'];
}
