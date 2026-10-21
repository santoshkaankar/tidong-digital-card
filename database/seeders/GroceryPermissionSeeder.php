<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/** Definitions only. Never assigns employee permissions or grants access. Optional, idempotent. */
class GroceryPermissionSeeder extends Seeder
{
    private const CODES = [
        'grocery.view' => 'View Grocery Workspace',
        'grocery.catalogue.manage' => 'Manage Grocery Catalogue',
        'grocery.pricing.manage' => 'Manage Grocery Pricing',
        'grocery.inventory.receive' => 'Receive Grocery Stock',
        'grocery.counter_sales.record' => 'Record Grocery Counter Sale',
        'grocery.counter_sales.reverse' => 'Reverse Grocery Counter Sale',
        'grocery.reports.view' => 'View Grocery Reports',
        'grocery.branch_settings.manage' => 'Manage Branch Grocery Settings',
    ];

    public function run(): void
    {
        foreach (self::CODES as $code => $name) {
            DB::table('permissions')->insertOrIgnore([
                'code' => $code, 'name' => $name, 'module' => 'grocery',
                'description' => 'Reserved for a future business/branch-scoped employee authorization rollout.',
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }
}
