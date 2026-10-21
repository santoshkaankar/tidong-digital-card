<?php
namespace App\Services\Grocery;

use InvalidArgumentException;

/** Precise: paise and thousandths, with GST stored as percentage basis points. */
final class GroceryMath
{
    public static function paise(string $value): int
    {
        if (!preg_match('/^\d{1,7}(?:\.\d{1,2})?$/D', $value)) {
            throw new InvalidArgumentException('Amount must have up to two decimal places.');
        }
        [$rupees, $cents] = array_pad(explode('.', $value, 2), 2, '');
        return (int) $rupees * 100 + (int) str_pad($cents, 2, '0');
    }

    public static function milli(string $value): int
    {
        if (!preg_match('/^\d{1,7}(?:\.\d{1,3})?$/D', $value)) {
            throw new InvalidArgumentException('Quantity must have up to three decimal places.');
        }
        [$units, $fraction] = array_pad(explode('.', $value, 2), 2, '');
        return (int) $units * 1000 + (int) str_pad($fraction, 3, '0');
    }

    public static function formatPaise(int $paise): string
    {
        return number_format($paise / 100, 2, '.', '');
    }

    public static function formatMilli(int $milli): string
    {
        return number_format($milli / 1000, 3, '.', '');
    }

    public static function line(int $pricePaise, int $quantityMilli, int $gstBps, bool $inclusive, bool $interstate): array
    {
        if ($pricePaise <= 0 || $quantityMilli <= 0 || $gstBps < 0 || $gstBps > 4000) {
            throw new InvalidArgumentException('Invalid price, quantity or GST rate.');
        }
        // Half-up per invoice line; intermediate products within signed 64-bit bounds for validated inputs.
        $priced = intdiv($pricePaise * $quantityMilli + 500, 1000);
        if ($inclusive) {
            $gross = $priced;
            $tax = intdiv($gross * $gstBps + intdiv(10000 + $gstBps, 2), 10000 + $gstBps);
            $taxable = $gross - $tax;
        } else {
            $taxable = $priced;
            $tax = intdiv($taxable * $gstBps + 5000, 10000);
            $gross = $taxable + $tax;
        }
        $cgst = $interstate ? 0 : intdiv($tax, 2);
        $sgst = $interstate ? 0 : $tax - $cgst;
        return [
            'taxable_paise' => $taxable, 'cgst_paise' => $cgst, 'sgst_paise' => $sgst,
            'igst_paise' => $interstate ? $tax : 0, 'tax_paise' => $tax, 'total_paise' => $gross,
        ];
    }
}
