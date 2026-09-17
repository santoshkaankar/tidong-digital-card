<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\Payment\VendorWallet;
use App\Models\Payment\WalletTransaction;
use App\Models\Member;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    // ==========================================
    // 1. RESTAURANT DAILY DEDUCTIONS (₹1/day)
    // ==========================================
    $wallets = VendorWallet::all();
    
    foreach ($wallets as $wallet) {
        $deductAmount = 1.00;
        
        if ($wallet->bonus_balance >= $deductAmount) {
            $wallet->decrement('bonus_balance', $deductAmount);
            $walletType = 'bonus';
        } else {
            $wallet->decrement('sales_balance', $deductAmount);
            $walletType = 'sales';
        }

        WalletTransaction::create([
            'vendor_id'   => $wallet->vendor_id,
            'wallet_type' => $walletType,
            'type'        => 'debit',
            'amount'      => $deductAmount,
            'description' => 'Daily Platform SaaS Fee (₹1/day)',
            'status'      => 'success'
        ]);
    }

    // ==========================================
    // 2. MEMBER DAILY STAGE-WISE DEDUCTIONS (14 - stages, min ₹1, T Coin only)
    // ==========================================
    $members = Member::all();
    
    foreach ($members as $member) {
        $rsWalletBalance = $member->rs_wallet_balance ?? 0;
        if ($rsWalletBalance > 0) {
            continue; 
        }

        $stages = $member->stages ?? 0;
        $deductRs = max(1, 14 - $stages); 

        $tCoinBalance = $member->t_coin_balance ?? 0;
        if ($tCoinBalance >= $deductRs) {
            $member->decrement('t_coin_balance', $deductRs);
        }
    }

    // ==========================================
    // 3. MONTHLY ROYALTY FORMULA CALCULATION & WALLET AUTO-CREDIT
    // ==========================================
    $royaltyEligibleMembers = Member::where('stages', '>=', 14)->get();
    
    foreach ($royaltyEligibleMembers as $member) {
        // Agar royalty abhi tak calculate nahi hui hai toh pehle calculate karke save karein
        if (!$member->royalty_active) {
            $joiningDate = \Carbon\Carbon::parse($member->created_at);
            $baseJoinDate = \Carbon\Carbon::create(2026, 9, 1);
            $joiningDelayMonths = max(0, $baseJoinDate->diffInMonths($joiningDate));

            $completionDate = \Carbon\Carbon::parse($member->stage_14_completed_at ?? now());
            $achievementDelayMonths = max(0, $joiningDate->diffInMonths($completionDate));

            $maxRoyalty = 200000;
            $totalReduction = ($joiningDelayMonths * 1000) + ($achievementDelayMonths * 1000);
            $finalRoyalty = max(5000, $maxRoyalty - $totalReduction);

            $member->update([
                'monthly_royalty_amount' => $finalRoyalty,
                'royalty_active' => true
            ]);
        }

        // Har mahine ki 1 tareeq ko auto wallet mein add karne ka logic
        if (now()->day === 1 && $member->monthly_royalty_amount > 0) {
            // Member ke RS Wallet (sales/main wallet) mein amount increment karein
            $member->increment('rs_wallet_balance', $member->monthly_royalty_amount);

            // Agar member ki wallet transaction table ho toh yahan record bhi bana sakte hain
            /*
            MemberWalletTransaction::create([
                'member_id' => $member->id,
                'type' => 'credit',
                'amount' => $member->monthly_royalty_amount,
                'description' => 'Monthly Lifetime Royalty Credited - ' . now()->format('F Y'),
                'status' => 'success'
            ]);
            */
        }
    }

})->dailyAt('00:00');