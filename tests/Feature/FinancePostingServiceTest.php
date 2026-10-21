<?php

namespace Tests\Feature;

use App\Models\FinanceAccount;
use App\Models\FinanceLedgerGroup;
use App\Services\Finance\FinancePostingService;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Tests\TestCase;

class FinancePostingServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        DB::beginTransaction();
    }

    protected function tearDown(): void
    {
        if (DB::transactionLevel() > 0) {
            DB::rollBack();
        }

        parent::tearDown();
    }

    private function makeAccount(string $nature, string $currency = 'INR'): FinanceAccount
    {
        $suffix = strtoupper(bin2hex(random_bytes(5)));

        $group = FinanceLedgerGroup::create([
            'group_code' => 'TG-' . $suffix,
            'group_name' => 'Test Group ' . $suffix,
            'nature' => $nature,
            'currency' => $currency,
            'is_active' => true,
        ]);

        return FinanceAccount::create([
            'ledger_code' => 'TL-' . $suffix,
            'ledger_name' => 'Test Ledger ' . $suffix,
            'ledger_group_id' => $group->id,
            'currency' => $currency,
            'account_type' => 'system',
            'owner_type' => 'system',
            'opening_balance' => '0.00',
            'current_balance' => '0.00',
            'status' => 'active',
        ]);
    }

    private function transactionData(string $currency = 'INR'): array
    {
        return [
            'transaction_ref' => 'TEST-' . strtoupper(bin2hex(random_bytes(6))),
            'transaction_type' => 'test',
            'currency' => $currency,
            'description' => 'Finance automated test',
        ];
    }

    public function test_balanced_transaction_posts_successfully(): void
    {
        $asset = $this->makeAccount('asset');
        $income = $this->makeAccount('income');

        $transaction = app(FinancePostingService::class)->createAndPost(
            $this->transactionData(),
            [
                [
                    'ledger_id' => $asset->id,
                    'entry_type' => 'debit',
                    'amount' => '100.00',
                    'currency' => 'INR',
                ],
                [
                    'ledger_id' => $income->id,
                    'entry_type' => 'credit',
                    'amount' => '100.00',
                    'currency' => 'INR',
                ],
            ]
        );

        $this->assertSame('posted', $transaction->status);
        $this->assertCount(2, $transaction->entries);
        $this->assertSame('100.0000', $asset->fresh()->current_balance);
        $this->assertSame('100.0000', $income->fresh()->current_balance);
    }

    public function test_unbalanced_transaction_is_rejected(): void
    {
        $asset = $this->makeAccount('asset');
        $income = $this->makeAccount('income');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Debit and credit totals must be equal.');

        app(FinancePostingService::class)->createAndPost(
            $this->transactionData(),
            [
                [
                    'ledger_id' => $asset->id,
                    'entry_type' => 'debit',
                    'amount' => '100.00',
                    'currency' => 'INR',
                ],
                [
                    'ledger_id' => $income->id,
                    'entry_type' => 'credit',
                    'amount' => '90.00',
                    'currency' => 'INR',
                ],
            ]
        );
    }

    public function test_wrong_entry_currency_is_rejected(): void
    {
        $asset = $this->makeAccount('asset');
        $income = $this->makeAccount('income');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Every entry must use the transaction currency.');

        app(FinancePostingService::class)->createAndPost(
            $this->transactionData('INR'),
            [
                [
                    'ledger_id' => $asset->id,
                    'entry_type' => 'debit',
                    'amount' => '100.00',
                    'currency' => 'INR',
                ],
                [
                    'ledger_id' => $income->id,
                    'entry_type' => 'credit',
                    'amount' => '100.00',
                    'currency' => 'TCOIN',
                ],
            ]
        );
    }

    public function test_missing_ledger_is_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('One or more ledger accounts do not exist.');

        app(FinancePostingService::class)->createAndPost(
            $this->transactionData(),
            [
                [
                    'ledger_id' => 999999999,
                    'entry_type' => 'debit',
                    'amount' => '25.00',
                    'currency' => 'INR',
                ],
                [
                    'ledger_id' => 999999998,
                    'entry_type' => 'credit',
                    'amount' => '25.00',
                    'currency' => 'INR',
                ],
            ]
        );
    }
}
