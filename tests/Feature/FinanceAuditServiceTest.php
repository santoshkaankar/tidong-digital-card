<?php

namespace Tests\Feature;

use App\Models\FinanceAuditLog;
use App\Services\Finance\FinanceAuditService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class FinanceAuditServiceTest extends TestCase
{
    use DatabaseTransactions;

    public function test_audit_record_is_saved_successfully(): void
    {
        $service = new FinanceAuditService();

        $log = $service->record(
            action: 'finance.test',
            entityType: 'finance_transaction',
            entityId: 987654,
            reason: 'Automated test',
            oldValues: ['status' => 'draft'],
            newValues: ['status' => 'posted'],
            metadata: ['source' => 'automated_test']
        );

        $this->assertInstanceOf(FinanceAuditLog::class, $log);
        $this->assertDatabaseHas('finance_audit_logs', [
            'id' => $log->id,
            'action' => 'finance.test',
            'entity_type' => 'finance_transaction',
            'entity_id' => 987654,
            'reason' => 'Automated test',
        ]);

        $this->assertSame('draft', $log->old_values['status']);
        $this->assertSame('posted', $log->new_values['status']);
        $this->assertSame('automated_test', $log->metadata['source']);
    }
}