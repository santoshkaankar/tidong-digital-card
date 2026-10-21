<?php

namespace App\Services\Opening;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/** Outlet-owner only. Accounting rows stay in the existing finance tables. */
final class OutletOpeningService
{
    public function ownedOutlet(int $actor, int $business, int $branch, bool $lock = false): object
    {
        $q = DB::table('branches as br')->join('businesses as bu', 'bu.id', '=', 'br.business_id')
            ->join('vendors as ve', 've.id', '=', 'bu.vendor_id')
            ->where('br.id', $branch)->where('bu.id', $business)->where('ve.user_id', $actor)
            ->where('ve.status', 'approved')->where('bu.status', 'approved')->where('br.status', 'active')
            ->whereNull('br.deleted_at')->whereNull('bu.deleted_at')->whereNull('ve.deleted_at')
            ->whereIn('bu.business_type', ['restaurant', 'cafe', 'sublimation', 'custom_printing', 'printing'])
            ->select('br.id', 'br.name', 'bu.id as business_id', 'bu.name as business_name', 'bu.business_type');
        return ($lock ? $q->lockForUpdate() : $q)->firstOrFail();
    }

    private function unlockedState(int $branch): object
    {
        $state = DB::table('outlet_opening_states')->where('branch_id', $branch)->lockForUpdate()->first();
        if (! $state) {
            throw ValidationException::withMessages(['opening_date' => 'Set the outlet opening date first.']);
        }
        if ($state->is_locked) {
            throw ValidationException::withMessages(['opening' => 'Opening is locked. The vendor owner must unlock it with a reason.']);
        }
        return $state;
    }

    private function audit(int $actor, int $business, int $branch, string $action, string $entity, ?int $id,
        ?array $old, ?array $new, string $reason, ?string $ip): void
    {
        DB::table('outlet_opening_audit_logs')->insert([
            'business_id' => $business, 'branch_id' => $branch, 'actor_user_id' => $actor,
            'action' => $action, 'entity_type' => $entity, 'entity_id' => $id,
            'old_values' => $old === null ? null : json_encode($old, JSON_THROW_ON_ERROR),
            'new_values' => $new === null ? null : json_encode($new, JSON_THROW_ON_ERROR),
            'reason' => $reason, 'ip_address' => $ip, 'created_at' => now(),
        ]);
    }

    public function begin(int $actor, int $business, int $branch, string $date, ?string $ip): void
    {
        DB::transaction(function () use ($actor, $business, $branch, $date, $ip): void {
            $this->ownedOutlet($actor, $business, $branch, true);
            if (DB::table('outlet_opening_states')->where('branch_id', $branch)->exists()) {
                throw ValidationException::withMessages(['opening_date' => 'Opening date was already set.']);
            }
            DB::table('outlet_opening_states')->insert([
                'business_id' => $business, 'branch_id' => $branch, 'opening_date' => $date,
                'created_by' => $actor, 'created_at' => now(), 'updated_at' => now(),
            ]);
            $this->audit($actor, $business, $branch, 'opening.started', 'outlet', $branch,
                null, ['opening_date' => $date], 'Vendor set initial opening date', $ip);
        }, 3);
    }

    public function setLock(int $actor, int $business, int $branch, bool $lock, string $reason, ?string $ip): void
    {
        DB::transaction(function () use ($actor, $business, $branch, $lock, $reason, $ip): void {
            $this->ownedOutlet($actor, $business, $branch, true);
            $state = DB::table('outlet_opening_states')->where('branch_id', $branch)->lockForUpdate()->firstOrFail();
            if ((bool) $state->is_locked === $lock) {
                throw ValidationException::withMessages(['opening' => 'Opening already has the requested lock state.']);
            }
            if ($lock) {
                // Do not certify an opening when pre-existing branch accounts were skipped.
                $unregistered = DB::table('finance_accounts as a')
                    ->where('a.owner_type', 'branch')->where('a.owner_id', $branch)
                    ->whereNull('a.deleted_at')->where('a.status', 'active')
                    ->whereNotIn('a.id', DB::table('outlet_ledger_opening_lines')->select('ledger_id'))
                    ->exists();
                if ($unregistered) {
                    throw ValidationException::withMessages(['opening' => 'Register every existing outlet ledger before final opening lock.']);
                }
                $foreignCurrency = DB::table('finance_accounts')->where('owner_type', 'branch')
                    ->where('owner_id', $branch)->where('status', 'active')->whereNull('deleted_at')
                    ->where('currency', '!=', 'INR')->exists();
                if ($foreignCurrency) {
                    throw ValidationException::withMessages(['opening' => 'Foreign-currency ledger opening is not supported in this review version.']);
                }
                $totals = DB::table('outlet_ledger_opening_lines')->where('branch_id', $branch)
                    ->selectRaw('direction, SUM(amount_paise) as total')->groupBy('direction')->pluck('total', 'direction');
                // A financial opening must balance in both directions before final lock.
                if ((string) ($totals['debit'] ?? 0) !== (string) ($totals['credit'] ?? 0)) {
                    throw ValidationException::withMessages(['opening' => 'Debit and Credit opening totals must balance before final lock.']);
                }
            }
            DB::table('outlet_opening_states')->where('branch_id', $branch)->update([
                'is_locked' => $lock, 'lock_version' => $state->lock_version + 1,
                'locked_by' => $lock ? $actor : null, 'locked_at' => $lock ? now() : null,
                'updated_at' => now(),
            ]);
            $this->audit($actor, $business, $branch, $lock ? 'opening.locked' : 'opening.unlocked',
                'outlet', $branch, ['locked' => (bool) $state->is_locked], ['locked' => $lock, 'version' => $state->lock_version + 1], $reason, $ip);
        }, 3);
    }

    public function createLedger(int $actor, int $business, int $branch, string $name, int $groupId, ?string $ip): void
    {
        DB::transaction(function () use ($actor, $business, $branch, $name, $groupId, $ip): void {
            $this->ownedOutlet($actor, $business, $branch, true);
            // New ledgers remain possible after opening lock; their opening stays zero.
            $group = DB::table('finance_ledger_groups')->where('id', $groupId)->where('is_active', true)->firstOrFail();
            if (($group->currency !== null && strtoupper($group->currency) !== 'INR')
                || ! in_array($group->nature, ['asset', 'expense', 'liability', 'equity', 'income'], true)) {
                throw ValidationException::withMessages(['group' => 'Unsupported ledger group nature.']);
            }
            $code = 'OUT'.$branch.'-'.strtoupper(Str::random(14));
            $id = DB::table('finance_accounts')->insertGetId([
                'ledger_code' => $code, 'ledger_name' => $name, 'ledger_group_id' => $groupId,
                'currency' => 'INR', 'account_type' => 'general', 'owner_type' => 'branch',
                'owner_id' => $branch, 'opening_balance' => 0, 'current_balance' => 0,
                'status' => 'active', 'created_by' => $actor, 'created_at' => now(), 'updated_at' => now(),
            ]);
            DB::table('outlet_ledger_opening_lines')->insert([
                'branch_id' => $branch, 'ledger_id' => $id, 'direction' => 'debit',
                'amount_paise' => 0, 'updated_by' => $actor, 'created_at' => now(), 'updated_at' => now(),
            ]);
            $this->audit($actor, $business, $branch, 'opening.ledger.created', 'finance_account', $id,
                null, ['ledger_name' => $name, 'group_id' => $groupId], 'Ledger created under outlet', $ip);
        }, 3);
    }

    /** Register an existing branch-owned INR account without overwriting a previous opening. */
    public function attachExistingLedger(int $actor, int $business, int $branch, int $ledger, ?string $ip): void
    {
        DB::transaction(function () use ($actor, $business, $branch, $ledger, $ip): void {
            $this->ownedOutlet($actor, $business, $branch, true);
            $this->unlockedState($branch);
            $account = DB::table('finance_accounts')->where('id', $ledger)
                ->where('owner_type', 'branch')->where('owner_id', $branch)
                ->where('currency', 'INR')->where('status', 'active')->whereNull('deleted_at')
                ->lockForUpdate()->firstOrFail();
            if (DB::table('outlet_ledger_opening_lines')->where('ledger_id', $ledger)->exists()) {
                throw ValidationException::withMessages(['ledger' => 'Account is already registered for opening.']);
            }
            $group = DB::table('finance_ledger_groups')->where('id', $account->ledger_group_id)->firstOrFail();
            if (! in_array($group->nature, ['asset', 'expense', 'liability', 'equity', 'income'], true)) {
                throw ValidationException::withMessages(['ledger' => 'Unsupported account group.']);
            }
            $signed = $this->balancePaise((string) $account->opening_balance);
            $normalDebit = in_array($group->nature, ['asset', 'expense'], true);
            $direction = ($signed >= 0) === $normalDebit ? 'debit' : 'credit';
            DB::table('outlet_ledger_opening_lines')->insert([
                'branch_id' => $branch, 'ledger_id' => $ledger, 'direction' => $direction,
                'amount_paise' => abs($signed), 'updated_by' => $actor,
                'created_at' => now(), 'updated_at' => now(),
            ]);
            $this->audit($actor, $business, $branch, 'opening.ledger.linked', 'finance_account', $ledger,
                null, ['direction' => $direction, 'amount_paise' => abs($signed)],
                'Existing branch account registered without balance changes', $ip);
        }, 3);
    }

    private function balancePaise(string $decimal): int
    {
        if (! preg_match('/^(-?)(\d{1,14})\.(\d{4})$/D', $decimal, $m) || substr($m[3], 2) !== '00') {
            throw ValidationException::withMessages(['amount' => 'Ledger balances must be accurate to a paise before editing opening.']);
        }
        $paise = (int) $m[2] * 100 + (int) substr($m[3], 0, 2);
        return $m[1] === '-' ? -$paise : $paise;
    }

    public function setLedgerOpening(int $actor, int $business, int $branch, int $ledger, string $direction,
        string $amount, string $reason, ?string $ip): void
    {
        DB::transaction(function () use ($actor, $business, $branch, $ledger, $direction, $amount, $reason, $ip): void {
            $this->ownedOutlet($actor, $business, $branch, true);
            $this->unlockedState($branch);
            $account = DB::table('finance_accounts')->where('id', $ledger)
                ->where('owner_type', 'branch')->where('owner_id', $branch)
                ->where('currency', 'INR')->where('status', 'active')->whereNull('deleted_at')
                ->lockForUpdate()->firstOrFail();
            $line = DB::table('outlet_ledger_opening_lines')->where('branch_id', $branch)
                ->where('ledger_id', $ledger)->lockForUpdate()->firstOrFail();
            $group = DB::table('finance_ledger_groups')->where('id', $account->ledger_group_id)->firstOrFail();
            $startDate = (string) DB::table('outlet_opening_states')->where('branch_id', $branch)->value('opening_date');
            if (DB::table('finance_ledger_entries as e')
                ->join('finance_transactions as t', 't.id', '=', 'e.transaction_id')
                ->where('e.ledger_id', $ledger)->where('t.status', 'posted')
                ->whereDate('t.posted_at', '<', $startDate)->exists()) {
                throw ValidationException::withMessages(['opening' => 'This ledger has posted entries before outlet opening date. Reconcile with a correction voucher; do not double count.']);
            }
            $old = $this->balancePaise((string) $account->opening_balance);
            $current = $this->balancePaise((string) $account->current_balance);
            $value = OpeningNumber::paise($amount);
            $natureDebit = in_array($group->nature, ['asset', 'expense'], true);
            $signed = (($direction === 'debit') === $natureDebit) ? $value : -$value;
            $next = $current + $signed - $old;
            if (abs($next) > 99999999999999) {
                throw ValidationException::withMessages(['amount' => 'Resulting ledger balance exceeds safe limit.']);
            }
            DB::table('finance_accounts')->where('id', $ledger)->update([
                'opening_balance' => OpeningNumber::formatPaise($signed),
                'current_balance' => OpeningNumber::formatPaise($next), 'updated_at' => now(),
            ]);
            DB::table('outlet_ledger_opening_lines')->where('id', $line->id)->update([
                'direction' => $direction, 'amount_paise' => $value, 'updated_by' => $actor, 'updated_at' => now(),
            ]);
            $this->audit($actor, $business, $branch, 'opening.ledger.changed', 'finance_account', $ledger,
                ['direction' => $line->direction, 'amount_paise' => $line->amount_paise, 'opening_balance' => (string) $account->opening_balance],
                ['direction' => $direction, 'amount_paise' => $value, 'opening_balance' => OpeningNumber::formatPaise($signed)], $reason, $ip);
        }, 3);
    }

    public function createStock(int $actor, int $business, int $branch, string $sku, string $name, string $unit, ?string $ip): void
    {
        DB::transaction(function () use ($actor, $business, $branch, $sku, $name, $unit, $ip): void {
            $this->ownedOutlet($actor, $business, $branch, true);
            // New physical items remain possible after opening lock; opening stays zero.
            $id = DB::table('outlet_stock_items')->insertGetId([
                'business_id' => $business, 'branch_id' => $branch, 'sku' => strtoupper($sku),
                'name' => $name, 'unit' => $unit, 'created_by' => $actor, 'created_at' => now(), 'updated_at' => now(),
            ]);
            $this->audit($actor, $business, $branch, 'opening.stock.created', 'stock_item', $id,
                null, ['sku' => strtoupper($sku), 'name' => $name, 'unit' => $unit], 'Stock item created', $ip);
        }, 3);
    }

    public function setStockOpening(int $actor, int $business, int $branch, int $item, string $quantity,
        string $cost, string $reason, ?string $ip): void
    {
        DB::transaction(function () use ($actor, $business, $branch, $item, $quantity, $cost, $reason, $ip): void {
            $this->ownedOutlet($actor, $business, $branch, true);
            $this->unlockedState($branch);
            $stock = DB::table('outlet_stock_items')->where('id', $item)->where('business_id', $business)
                ->where('branch_id', $branch)->lockForUpdate()->firstOrFail();
            $qty = OpeningNumber::quantityMillis($quantity);
            $costPaise = OpeningNumber::paise($cost);
            $value = OpeningNumber::stockValue($qty, $costPaise);
            $prior = OpeningNumber::quantityMillis((string) $stock->opening_quantity);
            $onHand = OpeningNumber::quantityMillis((string) $stock->quantity_on_hand);
            $updated = $onHand + $qty - $prior;
            if ($updated < 0 || $updated > 9999999999999) {
                throw ValidationException::withMessages(['quantity' => 'Opening change would make on-hand stock invalid. Record a stock correction instead.']);
            }
            DB::table('outlet_stock_items')->where('id', $item)->update([
                'opening_quantity' => OpeningNumber::formatQty($qty),
                'quantity_on_hand' => OpeningNumber::formatQty($updated),
                'opening_unit_cost_paise' => $costPaise, 'opening_stock_value_paise' => $value, 'updated_at' => now(),
            ]);
            $this->audit($actor, $business, $branch, 'opening.stock.changed', 'stock_item', $item,
                ['qty' => $stock->opening_quantity, 'unit_cost_paise' => $stock->opening_unit_cost_paise, 'on_hand' => $stock->quantity_on_hand],
                ['qty' => OpeningNumber::formatQty($qty), 'unit_cost_paise' => $costPaise, 'on_hand' => OpeningNumber::formatQty($updated)], $reason, $ip);
        }, 3);
    }

}
