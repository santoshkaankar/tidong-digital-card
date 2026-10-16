<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class WalletController extends Controller
{
    public function index()
    {
        $authUser = Auth::user();

        // Single-Wallet Flow: Child account shares parent wallet
        $effectiveUserId = ($authUser->is_child && $authUser->parent_user_id) 
            ? $authUser->parent_user_id 
            : $authUser->id;

        $wallet = DB::table('wallets')->where('user_id', $effectiveUserId)->first();

        if (!$wallet) {
            DB::table('wallets')->insert([
                'user_id'                  => $effectiveUserId,
                'real_balance'             => 0.00,
                'credit_limit'             => 500000.00, // ₹ 5 Lakh Limit
                'credit_used'              => 0.00,
                'non_withdrawable_balance' => 0.00,
                't_coins'                  => 25000.00,   // Initial 25,000 TCoins
                'credit_card_status'       => 'waiting',
                'credit_card_progress'     => 25,
                'created_at'               => now(),
                'updated_at'               => now(),
            ]);
            $wallet = DB::table('wallets')->where('user_id', $effectiveUserId)->first();
        }

        // --- DYNAMIC PROGRESS BAR & ACTIVATION LOGIC ---
        $userObj = DB::table('users')->where('id', $effectiveUserId)->first();
        
        $progress = 0;

        // 1. Aadhaar Verification (5%)
        if (isset($userObj->aadhaar_verified) && $userObj->aadhaar_verified) {
            $progress += 5;
        }

        // 2. Mobile Verification (5%)
        if (!empty($userObj->mobile)) {
            $progress += 5;
        }

        // 3. Email Verification (5%)
        if (!empty($userObj->email) && !str_contains($userObj->email, 'child_')) {
            $progress += 5;
        }

        // 4. MLM Stage Progress (Target: Stage 11 = max 50%)
        $currentStage = $userObj->mlm_stage ?? 0;
        $stageProgress = min(50, ($currentStage / 11) * 50);
        $progress += round($stageProgress);

        // 5. Game Tasks & Real Shopping Tasks (Default 15%)
        $tasksCompletedBonus = $userObj->tasks_bonus_progress ?? 15; 
        $progress += $tasksCompletedBonus;

        // Cap progress to max 100%
        $progress = min(100, $progress);

        // STRICT BACKEND RULE: 100% progress AUR Stage >= 11 hone par hi Card 'active' hoga!
        $isFullyActive = ($progress >= 100 && $currentStage >= 11);
        $cardStatus = $isFullyActive ? 'active' : 'waiting';

        // Update status in database table if columns exist
        if (Schema::hasColumn('wallets', 'credit_card_status')) {
            DB::table('wallets')->where('user_id', $effectiveUserId)->update([
                'credit_card_status'   => $cardStatus,
                'credit_card_progress' => $progress
            ]);
            $wallet->credit_card_status = $cardStatus;
            $wallet->credit_card_progress = $progress;
        } else {
            $wallet->credit_card_status = 'waiting';
            $wallet->credit_card_progress = $progress;
        }
        // -----------------------------------------------

        // Fetch INR Transactions History
        $transactions = DB::table('transactions')
                            ->where('user_id', $effectiveUserId)
                            ->orderBy('created_at', 'desc')
                            ->get();

        // Fetch T-Coin Transactions History
        $tCoinTransactions = DB::table('t_coin_transactions')
                                ->where('user_id', $effectiveUserId)
                                ->orderBy('created_at', 'desc')
                                ->get();

        // Fetch Credit Card Transactions History (if table exists)
        $creditCardTransactions = collect();
        if (Schema::hasTable('credit_card_transactions')) {
            $creditCardTransactions = DB::table('credit_card_transactions')
                                        ->where('user_id', $effectiveUserId)
                                        ->orderBy('created_at', 'desc')
                                        ->get();
        }

        return view('member.wallet.index', compact('wallet', 'transactions', 'tCoinTransactions', 'creditCardTransactions', 'authUser'));
    }
}