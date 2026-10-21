<?php

namespace Tests\Feature;

use App\Models\FinanceAccount;
use App\Models\FinanceLedgerGroup;
use App\Models\FinanceTransaction;
use App\Services\Finance\FinancePostingService;
use App\Services\Finance\FinanceReversalService;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Tests\TestCase;

class FinanceReversalServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        DB::beginTransaction();
    }

    protected function tearDown(): void
    {
        DB::rollBack();

        parent::tearDown();
    }

    public function test_posted_transaction_can_be_reversed(): void
    {
        $cash = $this->makeAccount(
            'CASH-REV-001',
            'Cash Reversal Test',
            'asset'
        );

        $income = $this->makeAccount(
            'INC-REV-001',
            'Income Reversal Test',
            'income'
        );

        $postingService = app(FinancePostingService::class);

        $original = $postingService->createAndPost(
            [
                'transaction_ref' => 'REV-TEST-001',
                'transaction_type' => 'sale',
                'currency' => 'INR',
                'description' => 'Original test transaction',
            ],
            [
                [
                    'ledger_id' => $cash->id,
                    'entry_type' => 'debit',
                    'amount' => '100.0000',
                    'currency' => 'INR',
                    'narration' => 'Original debit',
                ],
                [
                    'ledger_id' => $income->id,
                    'entry_type' => 'credit',
                    'amount' => '100.0000',
                    'currency' => 'INR',
                    'narration' => 'Original credit',
                ],
            ]
        );

        $this->assertSame('posted', $original->status);

        $this->assertSame(
            '100.0000',
            (string) $cash->fresh()->current_balance
        );

        $this->assertSame(
            '100.0000',
            (string) $income->fresh()->current_balance
        );

        $reversalService = app(FinanceReversalService::class);

        $reversal = $reversalService->reverse(
            $original,
            null,
            'Testing transaction reversal'
        );

        $this->assertSame('posted', $reversal->status);

        $this->assertSame(
            $original->id,
            $reversal->reverses_transaction_id
        );

        $this->assertSame(
            'reversal',
            $reversal->transaction_type
        );

        $this->assertCount(2, $reversal->entries);

        $this->assertSame(
            'credit',
            $reversal->entries
                ->firstWhere('ledger_id', $cash->id)
                ->entry_type
        );

        $this->assertSame(
            'debit',
            $reversal->entries
                ->firstWhere('ledger_id', $income->id)
                ->entry_type
        );

        $this->assertSame(
            '0.0000',
            (string) $cash->fresh()->current_balance
        );

        $this->assertSame(
            '0.0000',
            (string) $income->fresh()->current_balance
        );

        $this->assertSame(
            'posted',
            $original->fresh()->status
        );

        $this->assertDatabaseHas('finance_audit_logs', [
            'action' => 'finance.transaction.reversed',
            'entity_type' => 'finance_transaction',
            'entity_id' => $reversal->id,
            'reason' => 'Testing transaction reversal',
        ]);

        $this->assertDatabaseHas('finance_audit_logs', [
            'action' => 'finance.transaction.posted',
            'entity_type' => 'finance_transaction',
            'entity_id' => $original->id,
        ]);
    }

    public function test_draft_transaction_cannot_be_reversed(): void
    {
        $transaction = new FinanceTransaction();
        $transaction->transaction_ref = 'REV-DRAFT-001';
        $transaction->transaction_type = 'general';
        $transaction->currency = 'INR';
        $transaction->amount = '100.0000';
        $transaction->status = 'draft';
        $transaction->save();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Only a posted transaction can be reversed.'
        );

        app(FinanceReversalService::class)->reverse($transaction);
    }

    public function test_transaction_cannot_be_reversed_twice(): void
    {
        $cash = $this->makeAccount(
            'CASH-REV-002',
            'Cash Reversal Twice',
            'asset'
        );

        $income = $this->makeAccount(
            'INC-REV-002',
            'Income Reversal Twice',
            'income'
        );

        $original = app(FinancePostingService::class)->createAndPost(
            [
                'transaction_ref' => 'REV-TEST-002',
                'transaction_type' => 'sale',
                'currency' => 'INR',
            ],
            [
                [
                    'ledger_id' => $cash->id,
                    'entry_type' => 'debit',
                    'amount' => '50.00',
                    'currency' => 'INR',
                ],
                [
                    'ledger_id' => $income->id,
                    'entry_type' => 'credit',
                    'amount' => '50.00',
                    'currency' => 'INR',
                ],
            ]
        );

        $service = app(FinanceReversalService::class);

        $service->reverse($original);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'This transaction has already been reversed.'
        );

        $service->reverse($original->fresh());
    }

    public function test_reversal_transaction_cannot_be_reversed_again(): void
    {
        $cash = $this->makeAccount(
            'CASH-REV-003',
            'Cash Reversal Chain',
            'asset'
        );

        $income = $this->makeAccount(
            'INC-REV-003',
            'Income Reversal Chain',
            'income'
        );

        $original = app(FinancePostingService::class)->createAndPost(
            [
                'transaction_ref' => 'REV-TEST-003',
                'transaction_type' => 'sale',
                'currency' => 'INR',
            ],
            [
                [
                    'ledger_id' => $cash->id,
                    'entry_type' => 'debit',
                    'amount' => '25.00',
                    'currency' => 'INR',
                ],
                [
                    'ledger_id' => $income->id,
                    'entry_type' => 'credit',
                    'amount' => '25.00',
                    'currency' => 'INR',
                ],
            ]
        );

        $service = app(FinanceReversalService::class);

        $reversal = $service->reverse($original);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'This transaction is already a reversal transaction.'
        );

        $service->reverse($reversal);
    }

    private function makeAccount(
        string $ledgerCode,
        string $ledgerName,
        string $nature
    ): FinanceAccount {
        $group = FinanceLedgerGroup::create([
            'group_code' => 'GRP-' . $ledgerCode,
            'group_name' => $ledgerName . ' Group',
            'nature' => $nature,
            'currency' => 'INR',
            'is_active' => true,
        ]);

        return FinanceAccount::create([
            'ledger_code' => $ledgerCode,
            'ledger_name' => $ledgerName,
            'ledger_group_id' => $group->id,
            'currency' => 'INR',
            'account_type' => 'general',
            'owner_type' => 'system',
            'opening_balance' => '0.0000',
            'current_balance' => '0.0000',
            'status' => 'active',
        ]);
    }
}