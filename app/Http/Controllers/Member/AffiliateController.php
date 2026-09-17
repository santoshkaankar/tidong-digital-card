<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Wallet;

class AffiliateController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Table Existence Check
        $hasTransactions = Schema::hasTable('transactions');
        $hasUserPlans = !$hasTransactions && Schema::hasTable('user_plans');

        // Check if SELF transaction total is >= 25,000
        $myTotalShopping = $this->getUserShoppingTotal($user->id, $hasTransactions, $hasUserPlans, $user->total_purchase ?? 0);
        $isSelfEligible = ($myTotalShopping >= 25000);

        // Fetch Downlines (Leg A & Leg B)
        $legAUsers = $this->getLegUsers($user, ['left', 'L']);
        $legBUsers = $this->getLegUsers($user, ['right', 'R']);

        // Calculate total members and active (>= 25,000 transaction) counts
        [$activeA, $inactiveA] = $this->calculateLegStats($legAUsers, $hasTransactions, $hasUserPlans);
        [$activeB, $inactiveB] = $this->calculateLegStats($legBUsers, $hasTransactions, $hasUserPlans);

        $totalA = $activeA + $inactiveA;
        $totalB = $activeB + $inactiveB;
        $grandTotal = $totalA + $totalB;

        $stats = [
            'active_a'    => $activeA,
            'inactive_a'  => $inactiveA,
            'total_a'     => $totalA,
            'active_b'    => $activeB,
            'inactive_b'  => $inactiveB,
            'total_b'     => $totalB,
            'grand_total' => $grandTotal
        ];

        // Evaluate Stages based on joining and transaction conditions
        $this->evaluateStagesWithCondition($user, $totalA, $totalB, $activeA, $activeB, $isSelfEligible);

        // Fetch payouts history
        $rewards = DB::table('user_affiliate_payouts')
                    ->join('affiliate_stages', 'user_affiliate_payouts.stage_id', '=', 'affiliate_stages.id')
                    ->where('user_affiliate_payouts.user_id', $user->id)
                    ->select('user_affiliate_payouts.*', 'affiliate_stages.stage_name', 'affiliate_stages.stage_no')
                    ->get();

        return view('member.affiliates.referral', compact('user', 'stats', 'rewards', 'isSelfEligible'));
    }

    private function getLegUsers($user, array $positions)
    {
        return DB::table('users')
            ->where(function($query) use ($user) {
                $query->where('sponsor_id', $user->id)
                      ->orWhere('parent_id', $user->id);

                if (!empty($user->referral_id) && is_numeric($user->referral_id)) {
                    $query->orWhere('sponsor_id', $user->referral_id)
                          ->orWhere('parent_id', $user->referral_id);
                }
            })
            ->whereIn('position', $positions)
            ->get();
    }

    private function calculateLegStats($members, $hasTransactions, $hasUserPlans)
    {
        if ($members->isEmpty()) {
            return [0, 0];
        }

        $memberIds = $members->pluck('id')->toArray();
        $shoppingTotals = [];

        if ($hasTransactions) {
            $shoppingTotals = DB::table('transactions')
                ->whereIn('user_id', $memberIds)
                ->whereIn('status', ['success', 'completed', 'paid'])
                ->groupBy('user_id')
                ->selectRaw('user_id, SUM(amount) as total')
                ->pluck('total', 'user_id')
                ->toArray();
        } elseif ($hasUserPlans) {
            $shoppingTotals = DB::table('user_plans')
                ->whereIn('user_id', $memberIds)
                ->groupBy('user_id')
                ->selectRaw('user_id, SUM(amount) as total')
                ->pluck('total', 'user_id')
                ->toArray();
        }

        $active = 0;
        $inactive = 0;

        foreach ($members as $member) {
            $amount = $shoppingTotals[$member->id] ?? ($member->total_purchase ?? 0);
            if ($amount >= 25000) {
                $active++;
            } else {
                $inactive++;
            }
        }

        return [$active, $inactive];
    }

    private function getUserShoppingTotal($userId, $hasTransactions, $hasUserPlans, $defaultPurchase)
    {
        if ($hasTransactions) {
            return DB::table('transactions')
                ->where('user_id', $userId)
                ->whereIn('status', ['success', 'completed', 'paid'])
                ->sum('amount') ?? 0;
        } elseif ($hasUserPlans) {
            return DB::table('user_plans')
                ->where('user_id', $userId)
                ->sum('amount') ?? 0;
        }

        return $defaultPurchase;
    }

    /**
     * Fixed Deductions Calculation Helper
     * 10% Admin Charge + 5% TDS (with PAN) OR 20% TDS (without PAN)
     */
    private function calculateDeductions($grossAmount, $panNumber)
    {
        $adminCharge = $grossAmount * 0.10; // 10% Admin Charge
        $hasPan = !empty($panNumber);
        $tdsRate = $hasPan ? 0.05 : 0.20;  // 5% TDS if PAN present, else 20%
        $tdsAmount = $grossAmount * $tdsRate;
        $netAmount = $grossAmount - ($adminCharge + $tdsAmount);

        return [
            'gross'        => $grossAmount,
            'admin_charge' => $adminCharge,
            'tds_amount'   => $tdsAmount,
            'net_amount'   => $netAmount,
        ];
    }

    private function evaluateStagesWithCondition($user, $totalA, $totalB, $activeA, $activeB, $isSelfEligible)
    {
        $stages = DB::table('affiliate_stages')->get();

        foreach ($stages as $stage) {
            $exists = DB::table('user_affiliate_payouts')
                ->where('user_id', $user->id)
                ->where('stage_id', $stage->id)
                ->first();

            // Exact Deductions Calculation
            $deduction = $this->calculateDeductions($stage->incentive_amount, $user->pan_number);
            $gross       = $deduction['gross'];
            $adminCharge = $deduction['admin_charge'];
            $tdsAmount   = $deduction['tds_amount'];
            $netAmount   = $deduction['net_amount'];

            // Step 1: Member Joining Target Met -> Deduct T-Coins and move Net Amount to Reserved/Locked Wallet
            if (!$exists && $totalA >= $stage->leg_a_count && $totalB >= $stage->leg_b_count) {
                DB::transaction(function () use ($user, $stage, $gross, $adminCharge, $tdsAmount, $netAmount) {
                    DB::table('user_affiliate_payouts')->insert([
                        'user_id'      => $user->id,
                        'stage_id'     => $stage->id,
                        'gross_amount' => $gross,
                        'admin_charge' => $adminCharge,
                        'tds_amount'   => $tdsAmount,
                        'net_amount'   => $netAmount,
                        'status'       => 'locked',
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ]);

                    $wallet = Wallet::firstOrCreate(
                        ['user_id' => $user->id],
                        ['real_balance' => 0.00, 'non_withdrawable_balance' => 0.00, 't_coins' => 4540000.00]
                    );

                    $wallet->decrement('t_coins', $netAmount);
                    $wallet->increment('non_withdrawable_balance', $netAmount);
                });

                $exists = DB::table('user_affiliate_payouts')
                    ->where('user_id', $user->id)
                    ->where('stage_id', $stage->id)
                    ->first();
            }

            // Step 2: Transaction Criteria Met (Self >= 25k AND Leg A/B Members Active >= 25k) -> Move Locked Balance to Real Money Balance
            if ($exists && $exists->status == 'locked') {
                if ($isSelfEligible && $activeA >= $stage->leg_a_count && $activeB >= $stage->leg_b_count) {
                    DB::transaction(function () use ($exists, $user, $netAmount) {
                        DB::table('user_affiliate_payouts')
                            ->where('id', $exists->id)
                            ->update(['status' => 'unlocked', 'updated_at' => now()]);

                        $wallet = Wallet::firstOrCreate(
                            ['user_id' => $user->id],
                            ['real_balance' => 0.00, 'non_withdrawable_balance' => 0.00, 't_coins' => 4540000.00]
                        );

                        if ($wallet->non_withdrawable_balance >= $netAmount) {
                            $wallet->decrement('non_withdrawable_balance', $netAmount);
                        }
                        $wallet->increment('real_balance', $netAmount);
                    });
                }
            }
        }
    }
}