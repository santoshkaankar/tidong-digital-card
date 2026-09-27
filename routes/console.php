<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\Payment\VendorWallet;
use App\Models\Payment\WalletTransaction;
use App\Models\Member;
use App\Models\Wallet;
use Carbon\Carbon;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {

    // ==========================================
    // 1. RESTAURANT / VENDOR DAILY DEDUCTIONS (₹1/day)
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
    // 2. MEMBER DAILY STAGE-WISE DEDUCTIONS
    // Formula: (14 - Completed Stages) * ₹5 / day
    // ==========================================
    $members = Member::all();
    
    foreach ($members as $member) {
        $stages = min(14, max(0, (int) ($member->stages ?? 0)));
        $incompleteStages = 14 - $stages;
        $deductRs = $incompleteStages * 5; 

        if ($deductRs > 0) {
            $wallet = Wallet::firstOrCreate(
                ['user_id' => $member->id],
                [
                    'real_balance'             => 0.00, 
                    'non_withdrawable_balance' => 0.00, 
                    't_coins'                  => 4540000.00
                ]
            );

            // Continuous deduction into negative balance
            $wallet->decrement('non_withdrawable_balance', $deductRs);
        }
    }

    // ==========================================
    // 3. MONTHLY ROYALTY FORMULA CALCULATION
    // ==========================================
    $royaltyEligibleMembers = Member::where('stages', '>=', 14)->get();
    
    foreach ($royaltyEligibleMembers as $member) {
        if (!$member->royalty_active) {
            $joiningDate = Carbon::parse($member->created_at);
            $baseJoinDate = Carbon::create(2026, 9, 1);
            
            // FIX 1: Check if joining was AFTER base join date before applying penalty
            $joiningDelayMonths = $joiningDate->greaterThan($baseJoinDate) 
                ? $baseJoinDate->diffInMonths($joiningDate) 
                : 0;

            $completionDate = Carbon::parse($member->stage_14_completed_at ?? now());
            $achievementDelayMonths = $joiningDate->lessThan($completionDate)
                ? $joiningDate->diffInMonths($completionDate)
                : 0;

            $maxRoyalty = 200000;
            $totalReduction = ($joiningDelayMonths * 1000) + ($achievementDelayMonths * 1000);
            $finalRoyalty = max(5000, $maxRoyalty - $totalReduction);

            $member->update([
                'monthly_royalty_amount' => $finalRoyalty,
                'royalty_active'         => true
            ]);
        }

        // FIX 2: Credit directly to Wallet model instead of Member model
        if (now()->day === 1 && $member->monthly_royalty_amount > 0) {
            $wallet = Wallet::firstOrCreate(['user_id' => $member->id]);
            $wallet->increment('real_balance', $member->monthly_royalty_amount);
        }
    }

})->dailyAt('00:00');