<?php
namespace App\Support\LegalAdvisory;

use Illuminate\Support\Facades\DB;

final class Feature
{
    public const CODES = ['module','hiring','professional_portal'];

    public static function enabled(string $code): bool
    {
        // Default-deny: absent rows must never enable a feature.
        return in_array($code, self::CODES, true)
            && DB::table('legal_feature_flags')->where('code', 'module')->where('enabled', true)->exists()
            && ($code === 'module' || DB::table('legal_feature_flags')->where('code', $code)->where('enabled', true)->exists());
    }
}
