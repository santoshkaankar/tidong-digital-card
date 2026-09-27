<?php

namespace App\Services;

class OrderDeductionCalculator
{
    /**
     * Order par hone waale sabhi strict deductions calculate karta hai.
     *
     * @param float $orderAmount
     * @param string $paymentMethod ('online' / 'upi' / 'cod')
     * @return array
     */
    public static function calculate(float $orderAmount, string $paymentMethod = 'online'): array
    {
        // 1. Portal Commission (5% or Min ₹5)
        $commissionBase = max(5.00, $orderAmount * 0.05);
        $commissionGst  = $commissionBase * 0.18;
        $totalCommission = $commissionBase + $commissionGst;

        // 2. Payment Gateway / COD Handling Fee
        $isCod = strtolower($paymentMethod) === 'cod';
        $pgRate = $isCod ? 0.015 : 0.020; // COD: 1.5%, Online/UPI: 2.0%
        
        $pgBase = $orderAmount * $pgRate;
        if ($isCod && $pgBase < 2.00) {
            $pgBase = 2.00; // Minimum COD Handling Charge ₹2
        }

        $pgGst = $pgBase * 0.18;
        $totalPgCharge = $pgBase + $pgGst;

        // 3. Total Order Deductions
        $totalDeductions = $totalCommission + $totalPgCharge;
        $netVendorPayout = max(0.00, $orderAmount - $totalDeductions);

        return [
            'order_amount'      => round($orderAmount, 2),
            'payment_method'    => strtoupper($paymentMethod),
            
            // Commission Details
            'commission_base'   => round($commissionBase, 2),
            'commission_gst'    => round($commissionGst, 2),
            'total_commission'  => round($totalCommission, 2),
            
            // PG / COD Details
            'pg_base'           => round($pgBase, 2),
            'pg_gst'            => round($pgGst, 2),
            'total_pg_charge'   => round($totalPgCharge, 2),
            
            // Final Summary
            'total_deductions'  => round($totalDeductions, 2),
            'net_vendor_payout' => round($netVendorPayout, 2),
        ];
    }
}