<?php
namespace App\Services\Grocery;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

/** Global admin switch wins over every Vendor/Branch preference. Missing DB rows fail closed. */
final class GroceryFeatures
{
    public const LABELS = [
        'module' => 'Grocery Module',
        'catalogue' => 'Products & Catalogue',
        'pricing' => 'Offline / Online Prices',
        'inventory' => 'Inventory & Expiry Lots',
        'counter_sales' => 'Counter Billing',
        'reports' => 'Sales & Stock Reports',
        'online_store' => 'Customer Online Store',
        'online_orders' => 'Customer Order Checkout / Processing',
    ];

    public static function global(string $feature): bool
    {
        if (!array_key_exists($feature, self::LABELS) || !Schema::hasTable('grocery_feature_flags')) return false;
        return (bool) DB::table('grocery_feature_flags')->where('feature', $feature)->value('enabled');
    }

    public static function enabled(string $feature, ?int $branchId = null): bool
    {
        if (!self::global('module') || !self::global($feature)) return false;
        if ($branchId === null) return true;
        if (!Schema::hasTable('grocery_branch_features')) return false;
        $setting = DB::table('grocery_branch_features')->where('branch_id', $branchId)
            ->where('feature', $feature)->value('enabled');
        // Unless expressly opted out by the owner, active when platform allows.
        return $setting === null || (bool) $setting;
    }

    public static function requireEnabled(string $feature, ?int $branchId = null): void
    {
        abort_unless(self::enabled($feature, $branchId), 403, 'This Grocery option has been disabled.');
    }

    public static function writableForSale(int $branchId): void
    {
        self::requireEnabled('counter_sales', $branchId);
        self::requireEnabled('inventory', $branchId);
        self::requireEnabled('pricing', $branchId);
    }
}
