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

        // 1. Safe Check for User's own total shopping/spending
        $myTotalShopping = 0;
        if (Schema::hasTable('transactions')) {
            $myTotalShopping = DB::table('transactions')
                ->where('user_id', $user->id)
                ->where(function($q) {
                    $q->where('status', 'success')
                      ->orWhere('status', 'completed')
                      ->orWhere('status', 'paid');
                })->sum('amount') ?? 0;
        } elseif (Schema::hasTable('user_plans')) {
            $myTotalShopping = DB::table('user_plans')->where('user_id', $user->id)->sum('amount') ?? 0;
        } else {
            $myTotalShopping = $user->total_purchase ?? 0;
        }

        $isUserEligible = ($myTotalShopping >= 10000);

        // 2. Fetch Downlines based on AuthController logic (sponsor_id or parent_id matching user->id)
        $activeA = 0; $inactiveA = 0;
        $activeB = 0; $inactiveB = 0;

        // Leg A (Left Position)
        $legAUsers = DB::table('users')
            ->where(function($query) use ($user) {
                $query->where('sponsor_id', $user->id)
                      ->orWhere('parent_id', $user->id)
                      ->orWhere('sponsor_id', $user->referral_id)
                      ->orWhere('parent_id', $user->referral_id);
            })
            ->where(function($q) {
                $q->where('position', 'left')
                  ->orWhere('position', 'L');
            })
            ->get();

        foreach ($legAUsers as $member) {
            $memberShopping = 0;
            if (Schema::hasTable('transactions')) {
                $memberShopping = DB::table('transactions')
                    ->where('user_id', $member->id)
                    ->where(function($q) {
                        $q->where('status', 'success')
                          ->orWhere('status', 'completed')
                          ->orWhere('status', 'paid');
                    })->sum('amount') ?? 0;
            } elseif (Schema::hasTable('user_plans')) {
                $memberShopping = DB::table('user_plans')->where('user_id', $member->id)->sum('amount') ?? 0;
            } else {
                $memberShopping = $member->total_purchase ?? 0;
            }

            if ($memberShopping >= 10000) {
                $activeA++;
            } else {
                $inactiveA++;
            }
        }

        // Leg B (Right Position)
        $legBUsers = DB::table('users')
            ->where(function($query) use ($user) {
                $query->where('sponsor_id', $user->id)
                      ->orWhere('parent_id', $user->id)
                      ->orWhere('sponsor_id', $user->referral_id)
                      ->orWhere('parent_id', $user->referral_id);
            })
            ->where(function($q) {
                $q->where('position', 'right')
                  ->orWhere('position', 'R');
            })
            ->get();

        foreach ($legBUsers as $member) {
            $memberShopping = 0;
            if (Schema::hasTable('transactions')) {
                $memberShopping = DB::table('transactions')
                    ->where('user_id', $member->id)
                    ->where(function($q) {
                        $q->where('status', 'success')
                          ->orWhere('status', 'completed')
                          ->orWhere('status', 'paid');
                    })->sum('amount') ?? 0;
            } elseif (Schema::hasTable('user_plans')) {
                $memberShopping = DB::table('user_plans')->where('user_id', $member->id)->sum('amount') ?? 0;
            } else {
                $memberShopping = $member->total_purchase ?? 0;
            }

            if ($memberShopping >= 10000) {
                $activeB++;
            } else {
                $inactiveB++;
            }
        }

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

        // 3. Evaluate Stages and handle Locked/Unlocked status
        $this->evaluateStagesWithCondition($user, $activeA, $activeB, $isUserEligible);

        // 4. Fetch payouts history from database
        $rewards = DB::table('user_affiliate_payouts')
                    ->join('affiliate_stages', 'user_affiliate_payouts.stage_id', '=', 'affiliate_stages.id')
                    ->where('user_affiliate_payouts.user_id', $user->id)
                    ->select('user_affiliate_payouts.*', 'affiliate_stages.stage_name', 'affiliate_stages.stage_no')
                    ->get();

        return view('member.affiliates.referral', compact('user', 'stats', 'rewards', 'isUserEligible'));
    }

    private function evaluateStagesWithCondition($user, $activeA, $activeB, $isUserEligible)
    {
        $stages = DB::table('affiliate_stages')->get();

        foreach ($stages as $stage) {
            if ($activeA >= $stage->leg_a_count && $activeB >= $stage->leg_b_count) {
                
                $exists = DB::table('user_affiliate_payouts')
                    ->where('user_id', $user->id)
                    ->where('stage_id', $stage->id)
                    ->first();

                $gross = $stage->incentive_amount;
                $adminCharge = $gross * 0.10; 
                $hasPan = !empty($user->pan_number); 
                $tdsRate = $hasPan ? 0.05 : 0.20; 
                $tdsAmount = $gross * $tdsRate;
                $netAmount = $gross - ($adminCharge + $tdsAmount);

                if (!$exists) {
                    $status = $isUserEligible ? 'unlocked' : 'locked';

                    DB::transaction(function () use ($user, $stage, $gross, $adminCharge, $tdsAmount, $netAmount, $status, $isUserEligible) {
                        DB::table('user_affiliate_payouts')->insert([
                            'user_id'      => $user->id,
                            'stage_id'     => $stage->id,
                            'gross_amount' => $gross,
                            'admin_charge' => $adminCharge,
                            'tds_amount'   => $tdsAmount,
                            'net_amount'   => $netAmount,
                            'status'       => $status,
                            'created_at'   => now(),
                            'updated_at'   => now(),
                        ]);

                        if ($isUserEligible) {
                            $wallet = Wallet::firstOrCreate(
                                ['user_id' => $user->id],
                                ['real_balance' => 0.00, 'non_withdrawable_balance' => 0.00, 't_coins' => 0.00]
                            );
                            $wallet->increment('non_withdrawable_balance', $netAmount);
                        }
                    });
                } elseif ($exists->status == 'locked' && $isUserEligible) {
                    DB::transaction(function () use ($exists, $user, $netAmount) {
                        DB::table('user_affiliate_payouts')
                            ->where('id', $exists->id)
                            ->update(['status' => 'unlocked', 'updated_at' => now()]);

                        $wallet = Wallet::firstOrCreate(
                            ['user_id' => $user->id],
                            ['real_balance' => 0.00, 'non_withdrawable_balance' => 0.00, 't_coins' => 0.00]
                        );
                        $wallet->increment('non_withdrawable_balance', $netAmount);
                    });
                }
            }
        }
    }
}