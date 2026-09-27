<?php

namespace App\Services;

use App\Models\Wallet;
use Illuminate\Support\Facades\DB;

class MemberWalletService
{
    /**
     * Non-Withdrawable Reward ko Real Wallet me transfer karne se pehle 
     * Negative Balance auto-recover karega.
     */
    public static function creditToRealWallet(int $userId, float $rewardAmount): void
    {
        DB::transaction(function () use ($userId, $rewardAmount) {
            $wallet = Wallet::firstOrCreate(
                ['user_id' => $userId],
                ['real_balance' => 0.00, 'non_withdrawable_balance' => 0.00, 't_coins' => 4540000.00]
            );

            $payableToReal = $rewardAmount;

            if ($wallet->non_withdrawable_balance < 0) {
                $negativeDues = abs($wallet->non_withdrawable_balance);

                if ($payableToReal >= $negativeDues) {
                    $payableToReal -= $negativeDues;
                    $wallet->non_withdrawable_balance = 0.00;
                } else {
                    $wallet->non_withdrawable_balance += $payableToReal;
                    $payableToReal = 0.00;
                }
            }

            if ($payableToReal > 0) {
                $wallet->real_balance += $payableToReal;
            }

            $wallet->save();
        });
    }
}