<?php

namespace App\Services\Affiliate;

use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/** In-process adapter for a verified, refund-aware order event. No payable ledger/wallet writes. */
final class AffiliateEstimateService
{
    public function recordVerifiedSale(string $sourceReference, int $buyerUserId, int $eligibleSalePaise, bool $paymentVerified, bool $isRefunded = false): ?object
    {
        if (! $paymentVerified || $isRefunded) {
            return null;
        }
        if (! preg_match('/^[A-Za-z0-9_.:\-]{1,100}$/', $sourceReference) || $eligibleSalePaise < 1 || $eligibleSalePaise > 1000000000000) {
            throw new InvalidArgumentException('Invalid verified sale input.');
        }
        return DB::transaction(function () use ($sourceReference, $buyerUserId, $eligibleSalePaise): ?object {
            $settings = DB::table('affiliate_settings')->where('id', 1)->first();
            if (! $settings || ! $settings->referral_tracking_enabled || $settings->estimate_rate_bps < 1 || $settings->estimate_rate_bps > 10000) {
                return null;
            }
            $buyer = DB::table('affiliate_members')->where('user_id', $buyerUserId)->where('status', 'active')->first();
            if (! $buyer || ! $buyer->referrer_member_id) {
                return null;
            }
            $sponsor = DB::table('affiliate_members')->where('id', $buyer->referrer_member_id)->where('status', 'active')->first();
            if (! $sponsor) {
                return null;
            }
            // Integer half-up rounding, no float and no TDS calculation at this stage.
            $bps = (int) $settings->estimate_rate_bps;
            $estimatePaise = intdiv(($eligibleSalePaise * $bps) + 5000, 10000);
            $identity = [
                'source_kind' => 'verified_sale', 'source_reference' => $sourceReference,
                'member_id' => $sponsor->id,
            ];
            // Database uniqueness enforces idempotency even across concurrent workers.
            DB::table('affiliate_estimates')->insertOrIgnore($identity + [
                'referred_member_id' => $buyer->id, 'currency' => 'INR',
                'eligible_sale_paise' => $eligibleSalePaise, 'rate_bps' => $bps,
                'estimated_commission_paise' => $estimatePaise,
                'policy_version' => $settings->policy_version,
                'status' => 'estimate_only', 'verified_sale_at' => now(),
                'created_at' => now(), 'updated_at' => now(),
            ]);
            return DB::table('affiliate_estimates')->where($identity)->first();
        }, 3);
    }
}
