<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\Payment\VendorWallet;
use App\Models\Payment\WalletTransaction;
use App\Models\Member;
use App\Models\Wallet;

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
    // 2. MEMBER DAILY STAGE-WISE DEDUCTIONS (Reserved Reward Wallet Negative Balance Logic)
    // ==========================================
    $members = Member::all();
    
    foreach ($members as $member) {
        $stages = $member->stages ?? 0;
        // Formula: 14 - stages (minimum ₹1 per day cutting)
        $deductRs = max(1, 14 - $stages); 

        // Member ka wallet fetch karein ya create karein
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $member->id],
            ['real_balance' => 0.00, 'non_withdrawable_balance' => 0.00, 't_coins' => 4540000.00]
        );

        // Reserved Reward Wallet (non_withdrawable_balance) se daily deduction hogi
        // Yeh balance automatically negative (-ve) me jata rahega jab tak T-Coins convert hokar nahi aate
        $wallet->decrement('non_withdrawable_balance', $deductRs);
    }

    // ==========================================
    // 3. MONTHLY ROYALTY FORMULA CALCULATION & WALLET AUTO-CREDIT
    // ==========================================
    $royaltyEligibleMembers = Member::where('stages', '>=', 14)->get();
    
    foreach ($royaltyEligibleMembers as $member) {
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

        if (now()->day === 1 && $member->monthly_royalty_amount > 0) {
            $member->increment('rs_wallet_balance', $member->monthly_royalty_amount);
        }
    }

})->dailyAt('00:00');