<?php

namespace App\Services\Finance;

use App\Models\FinanceTransaction;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class FinanceReversalService
{
    public function reverse(
        FinanceTransaction $original,
        ?int $actorUserId = null,
        ?string $description = null
    ): FinanceTransaction {
        return DB::transaction(function () use (
            $original,
            $actorUserId,
            $description
        ) {
            $original = FinanceTransaction::query()
                ->with('entries')
                ->lockForUpdate()
                ->find($original->id);

            if (!$original) {
                throw new InvalidArgumentException(
                    'Original transaction does not exist.'
                );
            }

            if ($original->status !== 'posted') {
                throw new InvalidArgumentException(
                    'Only a posted transaction can be reversed.'
                );
            }

            if ($original->reverses_transaction_id !== null) {
                throw new InvalidArgumentException(
                    'This transaction is already a reversal transaction.'
                );
            }

            if ($original->reversals()->exists()) {
                throw new InvalidArgumentException(
                    'This transaction has already been reversed.'
                );
            }

            if ($original->entries->count() < 2) {
                throw new InvalidArgumentException(
                    'A posted transaction must have at least two ledger entries.'
                );
            }

            $reversalEntries = [];

            foreach ($original->entries as $entry) {
                $reversalEntries[] = [
                    'ledger_id' => $entry->ledger_id,
                    'entry_type' => $entry->entry_type === 'debit'
                        ? 'credit'
                        : 'debit',
                    'amount' => (string) $entry->amount,
                    'currency' => $entry->currency,
                    'narration' => 'Reversal of ' . $original->transaction_ref,
                ];
            }

            $reference = 'REV-' . strtoupper(bin2hex(random_bytes(8)));

            $postingService = new FinancePostingService();

            $transaction = $postingService->createAndPost(
                [
                    'transaction_ref' => $reference,
                    'idempotency_key' => 'REVERSAL-' . $original->id,
                    'transaction_type' => 'reversal',
                    'currency' => $original->currency,
                    'description' => $description
                        ?? 'Reversal of ' . $original->transaction_ref,
                    'metadata' => [
                        'reverses_transaction_id' => $original->id,
                        'reverses_transaction_ref' => $original->transaction_ref,
                    ],
                ],
                $reversalEntries,
                $actorUserId
            );

            $transaction->reverses_transaction_id = $original->id;
            $transaction->save();

            (new FinanceAuditService())->record(
                action: 'finance.transaction.reversed',
                entityType: 'finance_transaction',
                entityId: (int) $transaction->id,
                actorUserId: $actorUserId,
                reason: $description ?? 'Reversal of ' . $original->transaction_ref,
                oldValues: [
                    'original_transaction_id' => $original->id,
                    'original_transaction_ref' => $original->transaction_ref,
                    'original_status' => $original->status,
                ],
                newValues: [
                    'reversal_transaction_id' => $transaction->id,
                    'reversal_transaction_ref' => $transaction->transaction_ref,
                    'reversal_status' => $transaction->status,
                ],
                metadata: [
                    'original_transaction_id' => $original->id,
                    'original_transaction_ref' => $original->transaction_ref,
                ]
            );

            return $transaction->load('entries');
        }, 3);
    }
}