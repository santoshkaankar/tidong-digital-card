<?php

namespace App\Services\Customer;

/**
 * Stateless offline arithmetic answers. No persistence and no paid AI calls.
 * Never evaluates user-provided PHP or arbitrary expressions.
 */
final class TidongMathAnswers
{
    public static function answer(string $question): ?string
    {
        $q = trim(str_replace(['×', '÷', '−', '％'], ['*', '/', '-', '%'], $question));
        $q = preg_replace('/[?？।]+$/u', '', $q);
        if (preg_match('/^\s*(?:what is|calculate|solve|बताओ|निकालो|कितना होगा|हिसाब करो)?\s*(-?\d+(?:\.\d+)?)\s*(\+|\-|\*|\/|%|plus|minus|times|divided by|जोड़|घटा|गुणा|भाग)\s*(-?\d+(?:\.\d+)?)\s*(?:कितना होगा|कितने होंगे|का उत्तर|what is)?\s*$/iu', $q, $m)) {
            $a = (float) $m[1];
            $b = (float) $m[3];
            $op = mb_strtolower($m[2]);
            $result = match ($op) {
                '+', 'plus', 'जोड़' => $a + $b,
                '-', 'minus', 'घटा' => $a - $b,
                '*', 'times', 'गुणा' => $a * $b,
                '/', 'divided by', 'भाग' => $b == 0 ? null : $a / $b,
                '%' => $b == 0 ? null : fmod($a, $b),
                default => null,
            };
            if ($result === null) return 'शून्य से भाग या शेषफल निकालना संभव नहीं है।';
            if (!is_finite($result)) return 'इस गणना का परिणाम बहुत बड़ा है।';
            return 'उत्तर: '.($result == 0 ? '0' : ($result == 0 ? '0' : rtrim(rtrim(number_format($result, 6, '.', ''), '0'), '.')));
        }
        if (preg_match('/^\s*(\d+(?:\.\d+)?)\s*(?:का|of)?\s*(\d+(?:\.\d+)?)\s*(?:%|प्रतिशत|percent)\s*(?:कितना|क्या|होगा)?\s*$/iu', $q, $m)) {
            $result = (float)$m[1] * (float)$m[2] / 100;
            return 'उत्तर: '.rtrim(rtrim(number_format($result, 6, '.', ''), '0'), '.');
        }
        return null;
    }
}
