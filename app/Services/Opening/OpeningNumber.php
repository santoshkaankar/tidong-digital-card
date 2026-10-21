<?php

namespace App\Services\Opening;

use Illuminate\Validation\ValidationException;

final class OpeningNumber
{
    public static function paise(string $value): int
    {
        if (! preg_match('/^(?:0|[1-9]\d{0,11})(?:\.\d{1,2})?$/D', $value)) {
            throw ValidationException::withMessages(['amount' => 'Enter a valid non-negative INR amount (up to two decimals).']);
        }
        [$whole, $part] = array_pad(explode('.', $value, 2), 2, '');
        return (int) $whole * 100 + (int) str_pad($part, 2, '0');
    }

    public static function quantityMillis(string $value): int
    {
        if (! preg_match('/^(?:0|[1-9]\d{0,9})(?:\.\d{1,3})?$/D', $value)) {
            throw ValidationException::withMessages(['quantity' => 'Enter a quantity up to three decimals.']);
        }
        [$whole, $part] = array_pad(explode('.', $value, 2), 2, '');
        return (int) $whole * 1000 + (int) str_pad($part, 3, '0');
    }

    public static function formatQty(int $millis): string
    {
        return ($millis < 0 ? '-' : '').intdiv(abs($millis), 1000).'.'.str_pad((string) (abs($millis) % 1000), 3, '0', STR_PAD_LEFT);
    }

    public static function formatPaise(int $paise): string
    {
        return ($paise < 0 ? '-' : '').intdiv(abs($paise), 100).'.'.str_pad((string) (abs($paise) % 100), 2, '0', STR_PAD_LEFT);
    }

    public static function stockValue(int $quantityMillis, int $unitCostPaise): int
    {
        // Exact integer calculation; round half up to one paise, no floating-point currency.
        if ($quantityMillis < 0 || $unitCostPaise < 0 || ($unitCostPaise > 0 && $quantityMillis > intdiv(PHP_INT_MAX - 500, $unitCostPaise))) {
            throw ValidationException::withMessages(['amount' => 'Stock value exceeds the supported limit.']);
        }
        return intdiv($quantityMillis * $unitCostPaise + 500, 1000);
    }
}
