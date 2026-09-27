<?php

namespace App\Services;

class OrderCalculationService
{
    /**
     * Order ke total amount aur vendor payout ki poori calculation
     */
    public static function calculateOrderBreakdown(
        float $subtotal, 
        float $deliveryCharge = 0.00, 
        string $deliveryType = 'vendor', // 'vendor' ya 'platform'
        string $paymentMethod = 'upi'    // 'upi' ya 'cod'
    ): array {
        
        // 1. Gross Customer Total (Addition)
        $customerTotal = $subtotal + $deliveryCharge;

        // 2. Platform Commission (5% on Subtotal, Min ₹5)
        $commissionRate = 0.05;
        $commissionAmount = max(5.00, $subtotal * $commissionRate);
        $commissionGst = $commissionAmount * 0.18; // 18% GST

        // 3. Payment Gateway / COD Fee
        if ($paymentMethod === 'cod') {
            $pgFeeRate = 0.015; // 1.5% COD Handling
            $pgFeeAmount = max(2.00, $customerTotal * $pgFeeRate);
        } else {
            $pgFeeRate = 0.02;  // 2% UPI/PG Fee
            $pgFeeAmount = $customerTotal * $pgFeeRate;
        }
        $pgFeeGst = $pgFeeAmount * 0.18; // 18% GST

        // 4. Delivery Charge Deduction (Agar Delivery Platform handle kar raha hai)
        $deliveryDeduction = ($deliveryType === 'platform') ? $deliveryCharge : 0.00;

        // 5. Total Vendor Deductions
        $totalDeductions = $commissionAmount 
                         + $commissionGst 
                         + $pgFeeAmount 
                         + $pgFeeGst 
                         + $deliveryDeduction;

        // 6. Final Vendor Payout Amount
        $vendorPayout = $customerTotal - $totalDeductions;

        return [
            'subtotal'           => round($subtotal, 2),
            'delivery_charge'    => round($deliveryCharge, 2),
            'customer_total'     => round($customerTotal, 2),
            'commission'         => round($commissionAmount, 2),
            'commission_gst'     => round($commissionGst, 2),
            'pg_fee'             => round($pgFeeAmount, 2),
            'pg_fee_gst'         => round($pgFeeGst, 2),
            'delivery_deduction' => round($deliveryDeduction, 2),
            'total_deductions'   => round($totalDeductions, 2),
            'vendor_payout'      => round($vendorPayout, 2),
        ];
    }
}