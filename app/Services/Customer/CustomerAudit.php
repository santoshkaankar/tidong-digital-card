<?php

namespace App\Services\Customer;

use App\Models\Customer\CustomerAccountAuditLog;
use Illuminate\Http\Request;

final class CustomerAudit
{
    public static function record(Request $request, string $event, ?int $targetId = null): void
    {
        // Never store request bodies, passwords, or postal address details.
        CustomerAccountAuditLog::create([
            'user_id' => $request->user()->id,
            'event_code' => $event,
            'target_id' => $targetId,
            'ip_address' => $request->ip(),
            'created_at' => now(),
        ]);
    }
}
