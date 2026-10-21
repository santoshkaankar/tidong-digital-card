<?php

namespace App\Services\Affiliate;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class AffiliateMembershipService
{
    /**
     * Called inside a verified customer-registration transaction after the customer
     * account type is created, or once for an existing customer during reconciliation.
     * Sponsor attribution never changes after enrollment; placement is separate.
     */
    public function enroll(int $userId, ?string $code = null, ?string $leg = null): object
    {
        $code = strtoupper(trim((string) $code));
        $leg = strtoupper(trim((string) $leg));

        return DB::transaction(function () use ($userId, $code, $leg): object {
            // One singleton row lock serializes ALL placements across web workers.
            $settings = DB::table('affiliate_settings')->where('id', 1)->lockForUpdate()->first();
            if (! $settings || ! $settings->registration_enabled || $settings->fee_collection_enabled) {
                throw ValidationException::withMessages(['registration' => 'Customer enrollment is currently unavailable.']);
            }
            if (! $settings->root_member_id || ! $settings->a_chain_tail_member_id) {
                throw ValidationException::withMessages(['registration' => 'Network setup is being completed. Please try again later.']);
            }
            $user = DB::table('users')->where('id', $userId)->first();
            if (! $user || ! $this->isCustomer($userId)) {
                throw ValidationException::withMessages(['account' => 'An active customer account is required.']);
            }
            // The customer account must complete the platform's identity verification.
            if ($user->email_verified_at === null) {
                throw ValidationException::withMessages(['account' => 'Please verify your email to complete network registration.']);
            }
            $existing = DB::table('affiliate_members')->where('user_id', $userId)->first();
            if ($existing) {
                return $existing;
            }

            $sponsor = null;
            $parentId = null;
            $placementLeg = null;

            if ($code === '') {
                if ($leg !== '') {
                    throw ValidationException::withMessages(['leg' => 'A referral code is required for leg selection.']);
                }
                $parentId = (int) $settings->a_chain_tail_member_id;
                $placementLeg = 'A';
                // Check invariant, not just pointer presence.
                if (DB::table('affiliate_members')->where('placement_parent_member_id', $parentId)
                    ->where('placement_leg', 'A')->exists()) {
                    throw ValidationException::withMessages(['registration' => 'Network placement needs an administrator review.']);
                }
            } else {
                if (! $settings->referral_tracking_enabled || ! preg_match('/^[A-F0-9]{16}$/', $code)) {
                    throw ValidationException::withMessages(['referral_code' => 'Invalid referral code.']);
                }
                if (! in_array($leg, ['A', 'B'], true)) {
                    throw ValidationException::withMessages(['leg' => 'Choose Leg A or Leg B with your referral link.']);
                }
                $sponsor = DB::table('affiliate_members')->where('referral_code', $code)
                    ->where('status', 'active')->first();
                if (! $sponsor || (int) $sponsor->user_id === $userId) {
                    throw ValidationException::withMessages(['referral_code' => 'Invalid or self-referral code.']);
                }
                // The referred spillover policy is NOT owner-approved. Avoid silently
                // placing a user under an unrelated member when this slot is full.
                $parentId = (int) $sponsor->id;
                $placementLeg = $leg;
                if (DB::table('affiliate_members')->where('placement_parent_member_id', $parentId)
                    ->where('placement_leg', $placementLeg)->exists()) {
                    throw ValidationException::withMessages(['leg' => 'This placement leg is full. Choose an available slot.']);
                }
            }

            $id = DB::table('affiliate_members')->insertGetId([
                'user_id' => $userId, 'referral_code' => $this->uniqueReferralCode(),
                'referrer_member_id' => $sponsor?->id,
                'placement_parent_member_id' => $parentId, 'placement_leg' => $placementLeg,
                'status' => 'active', 'joined_at' => now(),
                'created_at' => now(), 'updated_at' => now(),
            ]);
            // A referred placement can also extend the root's all-A chain.
            if ($placementLeg === 'A' && $parentId === (int) $settings->a_chain_tail_member_id) {
                DB::table('affiliate_settings')->where('id', 1)->update([
                    'a_chain_tail_member_id' => $id, 'updated_at' => now(),
                ]);
            }
            $this->audit($userId, $id, 'affiliate.customer_auto_enrolled', [
                'actual_referrer_member_id' => $sponsor?->id,
                'placement_parent_member_id' => $parentId,
                'placement_leg' => $placementLeg,
                'fee_charged_paise' => 0,
            ]);
            return DB::table('affiliate_members')->where('id', $id)->first();
        }, 3);
    }

    /** Only a Master-authorized Super Admin may call this once for the genuine first ID. */
    public function initializeRoot(int $userId, int $actorUserId): object
    {
        return DB::transaction(function () use ($userId, $actorUserId): object {
            $settings = DB::table('affiliate_settings')->where('id', 1)->lockForUpdate()->first();
            if (! $settings || $settings->root_member_id || DB::table('affiliate_members')->exists()) {
                throw ValidationException::withMessages(['root' => 'Root setup requires a new, empty affiliate tree. Existing members need manual reconciliation.']);
            }
            if (! DB::table('users')->where('id', $userId)->exists() || ! $this->isCustomer($userId)) {
                throw ValidationException::withMessages(['user_id' => 'Choose a verified, active customer account for the first ID.']);
            }
            if (! DB::table('users')->where('id', $userId)->whereNotNull('email_verified_at')->exists()) {
                throw ValidationException::withMessages(['user_id' => 'The first ID must have a verified email.']);
            }
            $id = DB::table('affiliate_members')->insertGetId([
                'user_id' => $userId, 'referral_code' => $this->uniqueReferralCode(),
                'referrer_member_id' => null, 'placement_parent_member_id' => null,
                'placement_leg' => null, 'status' => 'active',
                'joined_at' => now(), 'created_at' => now(), 'updated_at' => now(),
            ]);
            DB::table('affiliate_settings')->where('id', 1)->update([
                'root_member_id' => $id, 'a_chain_tail_member_id' => $id,
                'updated_at' => now(),
            ]);
            $this->audit($actorUserId, $id, 'affiliate.root_initialized', ['root_member_id' => $id]);
            return DB::table('affiliate_members')->where('id', $id)->first();
        }, 3);
    }

    private function uniqueReferralCode(): string
    {
        do {
            $code = strtoupper(bin2hex(random_bytes(8)));
        } while (DB::table('affiliate_members')->where('referral_code', $code)->exists());
        return $code;
    }

    private function isCustomer(int $userId): bool
    {
        return DB::table('user_account_types as uat')
            ->join('account_types as at', 'at.id', '=', 'uat.account_type_id')
            ->where('uat.user_id', $userId)->where('uat.status', 'active')
            ->where('at.code', 'customer')->where('at.is_active', true)->exists();
    }

    private function audit(int $actorUserId, int $memberId, string $action, array $metadata): void
    {
        DB::table('affiliate_audit_events')->insert([
            'actor_user_id' => $actorUserId, 'member_id' => $memberId,
            'action' => $action, 'metadata' => json_encode($metadata, JSON_THROW_ON_ERROR),
            'created_at' => now(),
        ]);
    }
}
