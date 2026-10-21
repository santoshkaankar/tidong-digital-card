<?php

namespace App\Services\Finance;

use App\Models\FinanceAuditLog;
use Illuminate\Http\Request;

class FinanceAuditService
{
    public function record(
        string $action,
        string $entityType,
        ?int $entityId = null,
        ?int $actorUserId = null,
        ?string $reason = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?array $metadata = null,
        ?Request $request = null
    ): FinanceAuditLog {
        return FinanceAuditLog::query()->create([
            'actor_user_id' => $actorUserId,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'reason' => $reason,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'metadata' => $metadata,
            'ip_address' => $request?->ip(),
            'request_id' => $request?->header('X-Request-Id'),
            'created_at' => now(),
        ]);
    }
}