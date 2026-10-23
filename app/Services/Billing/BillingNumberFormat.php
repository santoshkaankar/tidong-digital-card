<?php

namespace App\Services\Billing;

use RuntimeException;

/**
 * Rule Book #14: voucher CODE/BOOK/SERIAL and common ID/CODE/BOOK/SERIAL.
 * Format is deliberately strict; number expansion requires an approved rule.
 */
final class BillingNumberFormat
{
    public static function compose(string $code, int $book, int $serial, bool $global = false): string
    {
        if (!in_array($code, OutletVoucherNumberingService::CODES, true)) {
            throw new RuntimeException('Unknown voucher code.');
        }
        if ($book < 1 || $book > 99 || $serial < 1 || $serial > 9999) {
            throw new RuntimeException('Billing numbering capacity reached; new Rule Book format required.');
        }

        $number = $code.'/'.str_pad((string) $book, 2, '0', STR_PAD_LEFT)
            .'/'.str_pad((string) $serial, 4, '0', STR_PAD_LEFT);

        return $global ? 'ID/'.$number : $number;
    }
}
