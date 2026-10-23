<?php

namespace App\Services\Auth;

use App\Services\Affiliate\AffiliateMembershipService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class RegistrationAffiliateIntent
{
    public function complete(int $userId): void
    {
        DB::transaction(function () use ($userId): void {
            $intent = DB::table('registration_affiliate_intents')->where('user_id', $userId)->lockForUpdate()->first();
            if (! $intent || $intent->status === 'completed') {
                return;
            }
            $verified = DB::table('users')->where('id', $userId)
                ->whereNotNull('email_verified_at')->exists();
            if (! $verified) {
                return;
            }
            try {
                // No money transfer is performed. This is binary placement ONLY.
                $code = $intent->referral_code;
                $leg = $intent->placement_leg;
                // Direct signup + Left uses the existing leftmost-chain algorithm.
                if (! $code && $leg === 'A') {
                    $leg = null;
                }
                app(AffiliateMembershipService::class)->enroll($userId, $code, $leg);
                DB::table('registration_affiliate_intents')->where('id', $intent->id)->update([
                    'status' => 'completed', 'last_error' => null, 'updated_at' => now(),
                ]);
            } catch (ValidationException $e) {
                DB::table('registration_affiliate_intents')->where('id', $intent->id)->update([
                    'status' => 'needs_attention',
                    'last_error' => mb_substr(implode(' ', array_merge(...array_values($e->errors()))), 0, 255),
                    'updated_at' => now(),
                ]);
            }
        }, 3);
    }
}
