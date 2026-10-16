<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class GameSubscriptionController extends Controller
{
    public function subscribeWithTCoins(Request $request)
    {
        $userId = Auth::id();
        $subscriptionCost = 199.00; // 199 T-Coins for 1 Year Pass

        // Fetch User Wallet
        $wallet = DB::table('wallets')->where('user_id', $userId)->first();

        if (!$wallet || $wallet->t_coins < $subscriptionCost) {
            return redirect()->back()->with('error', 'Insufficient T-Coins balance! Minimum 199 T-Coins required for Yearly Pass.');
        }

        // Deduct T-Coins from Wallet
        DB::table('wallets')->where('user_id', $userId)->decrement('t_coins', $subscriptionCost);

        // Log T-Coin Transaction
        DB::table('t_coin_transactions')->insert([
            'user_id'     => $userId,
            'description' => '3D Game Pro Yearly Subscription Pass',
            'type'        => 'debit',
            'amount'      => $subscriptionCost,
            'created_at'  => now(),
            'updated_at'  => now()
        ]);

        // Activate Subscription for 1 Year (365 Days)
        DB::table('game_subscriptions')->updateOrInsert(
            ['user_id' => $userId],
            [
                'status'     => 'active',
                'expires_at' => now()->addDays(365),
                'updated_at' => now()
            ]
        );

        return redirect()->to('/member/game/pro')->with('success', '🎉 3D Game Pro Yearly Pass Unlocked successfully for 365 days!');
    }
}