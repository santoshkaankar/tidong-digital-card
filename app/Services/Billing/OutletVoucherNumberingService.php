<?php

namespace App\Services\Billing;

use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;

/**
 * Internal allocation primitive. Caller MUST first authenticate the actor, authorize
 * voucher creation in business+outlet, and validate outlet/business ownership.
 * Use in the SAME DB transaction as voucher creation; never expose directly via HTTP.
 */
final class OutletVoucherNumberingService
{
    public function allocate(
        int $businessId,
        int $outletId,
        int $actorUserId,
        string $voucherCode,
        string $financialYear,
        string $requestKey
    ): array {
        if ($businessId < 1 || $outletId < 1 || $actorUserId < 1) {
            throw new InvalidArgumentException('Business, outlet and actor are required.');
        }
        if (!preg_match('/^[A-Z]{2}$/D', $voucherCode) || $voucherCode === 'GL') {
            throw new InvalidArgumentException('Voucher code must be two uppercase letters, excluding GL.');
        }
        if (!preg_match('/^\d{2}-\d{2}$/D', $financialYear)
            || ((int) substr($financialYear, 3, 2)) !== (((int) substr($financialYear, 0, 2) + 1) % 100)) {
            throw new InvalidArgumentException('Invalid financial year.');
        }
        if (!preg_match('/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[1-8][0-9a-fA-F]{3}-[89abAB][0-9a-fA-F]{3}-[0-9a-fA-F]{12}$/D', $requestKey)) {
            throw new InvalidArgumentException('A UUID request key is required.');
        }

        return DB::transaction(function () use ($businessId, $outletId, $actorUserId, $voucherCode, $financialYear, $requestKey) {
            $query = DB::table('billing_number_allocations')
                ->where('business_id', $businessId)->where('outlet_id', $outletId)->where('request_key', $requestKey);
            $existing = $query->first();
            if ($existing) {
                if ((int) $existing->actor_user_id !== $actorUserId || $existing->voucher_code !== $voucherCode || $existing->financial_year !== $financialYear) {
                    throw new InvalidArgumentException('Request key already used for a different voucher type or year.');
                }
                return $this->result($existing);
            }

            // Confirm outlet really belongs to this business (authorization is ALSO required by caller).
            if (!DB::table('branches')->where('id', $outletId)->where('business_id', $businessId)->exists()) {
                throw new InvalidArgumentException('Outlet does not belong to this business.');
            }

            // Counter rows are pre-provisioned by an authorized setup operation.
            // Lock in stable order; no race-prone create-if-absent logic in this path.
            $global = $this->counter($businessId, $outletId, 'GL');
            $typed = $this->counter($businessId, $outletId, $voucherCode);
            $g = $this->advance($global, $financialYear);
            $t = $this->advance($typed, $financialYear);

            $row = [
                'business_id' => $businessId, 'outlet_id' => $outletId,
                'actor_user_id' => $actorUserId, 'request_key' => $requestKey,
                'voucher_code' => $voucherCode, 'financial_year' => $financialYear,
                'global_number' => $g['number'], 'type_number' => $t['number'],
                'global_serial' => $g['serial'], 'type_serial' => $t['serial'],
                'created_at' => now(), 'updated_at' => now(),
            ];
            DB::table('billing_number_allocations')->insert($row);
            return $this->result((object) $row);
        }, 3);
    }

    private function counter(int $businessId, int $outletId, string $code): object
    {
        $counter = DB::table('billing_number_counters')
            ->where('business_id', $businessId)->where('outlet_id', $outletId)
            ->where('series_type', $code)->lockForUpdate()->first();
        if (!$counter) {
            throw new RuntimeException('Number series not configured for this outlet and voucher type.');
        }
        return $counter;
    }

    private function advance(object $counter, string $fy): array
    {
        $size = (int) $counter->book_size;
        if ($size < 1 || $size > 10000) {
            throw new RuntimeException('Invalid book size.');
        }
        if ($counter->last_fy !== null && strcmp($fy, $counter->last_fy) < 0) {
            throw new RuntimeException('Backdated financial year requires a separate approved process.');
        }
        $reset = (bool) $counter->reset_each_fy && $counter->last_fy !== null && $counter->last_fy !== $fy;
        $serial = $reset ? 1 : ((int) $counter->last_serial + 1);
        $book = $reset ? 1 : max(1, (int) $counter->last_book);
        $used = $reset ? 0 : (int) $counter->used_in_book;
        if ($used >= $size) {
            $book++;
            $used = 0;
        }
        // Preserve the current book's existing occupancy if book_size is changed.
        $used++;
        // Width is minimum, never truncate large identifiers.
        $number = $fy . '/' . str_pad((string) $book, 2, '0', STR_PAD_LEFT)
            . '/' . str_pad((string) $serial, 4, '0', STR_PAD_LEFT);
        DB::table('billing_number_counters')->where('id', $counter->id)->update([
            'last_serial' => $serial, 'last_book' => $book, 'used_in_book' => $used, 'last_fy' => $fy, 'updated_at' => now(),
        ]);
        return ['number' => $number, 'serial' => $serial];
    }

    private function result(object $row): array
    {
        return [
            'global_number' => $row->global_number,
            'type_number' => $row->type_number,
            'voucher_code' => $row->voucher_code,
            'financial_year' => $row->financial_year,
            'global_serial' => (int) $row->global_serial,
            'type_serial' => (int) $row->type_serial,
        ];
    }
}
