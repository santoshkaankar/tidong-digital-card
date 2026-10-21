<?php

namespace App\Services\Billing;

use InvalidArgumentException;

/**
 * Deterministic minor-unit calculation only. Amounts in paise, rates in basis points.
 * Tax rates must come from a separately authorized/effective-dated tax policy.
 * This calculator does not determine legal GST applicability or invoice eligibility.
 */
final class InvoiceAmountCalculator
{
    public function calculate(array $lines): array
    {
        if ($lines === [] || count($lines) > 500) {
            throw new InvalidArgumentException('Invoice must contain 1 to 500 lines.');
        }
        $results = [];
        $subtotal = $discountTotal = $taxableTotal = $taxTotal = $grandTotal = 0;
        foreach ($lines as $line) {
            foreach (['quantity_milli', 'unit_price_paise', 'discount_paise', 'tax_rate_bps'] as $field) {
                if (!array_key_exists($field, $line) || !is_int($line[$field])) {
                    throw new InvalidArgumentException('Integer value required: ' . $field);
                }
            }
            $quantity = $line['quantity_milli'];
            $price = $line['unit_price_paise'];
            $discount = $line['discount_paise'];
            $rate = $line['tax_rate_bps'];
            if ($quantity < 1 || $quantity > 100000000 || $price < 0 || $price > 100000000000 || $discount < 0 || $rate < 0 || $rate > 10000) {
                throw new InvalidArgumentException('Invoice amount or rate out of range.');
            }
            // Avoid floating point for money; integer half-up rounding.
            $base = intdiv($quantity * $price + 500, 1000);
            if ($discount > $base) {
                throw new InvalidArgumentException('Discount exceeds line value.');
            }
            $taxable = $base - $discount;
            $tax = intdiv($taxable * $rate + 5000, 10000);
            $gross = $taxable + $tax;
            $subtotal += $base;
            $discountTotal += $discount;
            $taxableTotal += $taxable;
            $taxTotal += $tax;
            $grandTotal += $gross;
            $results[] = ['base_paise'=>$base, 'discount_paise'=>$discount, 'taxable_paise'=>$taxable, 'tax_paise'=>$tax, 'total_paise'=>$gross];
        }
        if ($grandTotal > 100000000000000) {
            throw new InvalidArgumentException('Invoice value too large.');
        }
        return ['lines'=>$results, 'subtotal_paise'=>$subtotal, 'discount_paise'=>$discountTotal, 'taxable_paise'=>$taxableTotal, 'tax_paise'=>$taxTotal, 'grand_total_paise'=>$grandTotal];
    }
}
