<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Explicit, repeat-safe seed; never auto-seeds every vendor/branch.
 * Does not create an account, business, branch, order or price.
 */
class SeedSublimationSamples extends Command
{
    protected $signature = 'studio:seed-samples {business : Approved sublimation business ID} {branch : Branch ID belonging to that business} {--dry-run : Show selected target, without writing}';

    protected $description = 'Add inactive, unpriced sublimation sample products to ONE approved vendor branch.';

    public function handle(): int
    {
        if (!Schema::hasTable('sublimation_products')) {
            $this->error('Studio tables are not present. Run the V1 studio migration only after testing.');
            return self::FAILURE;
        }
        $businessId = filter_var($this->argument('business'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $branchId = filter_var($this->argument('branch'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($businessId === false || $branchId === false) {
            $this->error('Business and Branch must be positive numeric IDs.');
            return self::FAILURE;
        }
        $business = DB::table('businesses as b')
            ->join('vendors as v', 'v.id', '=', 'b.vendor_id')
            ->where('b.id', $businessId)
            ->whereIn('b.business_type', ['sublimation', 'custom_printing', 'printing'])
            ->where('b.status', 'approved')->where('v.status', 'approved')
            ->whereNull('b.deleted_at')->whereNull('v.deleted_at')
            ->select('b.id', 'b.name')->first();
        $branch = DB::table('branches')
            ->where('id', $branchId)->where('business_id', $businessId)
            ->where('status', 'active')->whereNull('deleted_at')
            ->select('id', 'name')->first();
        if (!$business || !$branch) {
            $this->error('Target must be an approved Sublimation Business and its ACTIVE branch. No records written.');
            return self::FAILURE;
        }
        $items = [
            ['Personalized Photo Mug', 'Mug'],
            ['Color Change Magic Mug', 'Mug'],
            ['Personalized Printed T-Shirt', 'T-Shirt'],
            ['Custom Logo Mouse Pad', 'Mouse Pad'],
            ['Photo Keychain', 'Keychain'],
            ['Personalized Photo Frame', 'Photo Frame'],
            ['Custom Photo Cushion', 'Cushion'],
            ['Printed Water Bottle', 'Bottle'],
            ['Personalized Coaster Set', 'Coaster'],
            ['Custom Printed Tote Bag', 'Tote Bag'],
            ['Photo Puzzle', 'Puzzle'],
            ['Custom Metal Photo Frame', 'Photo Frame'],
        ];
        $this->info("Selected: {$business->name} (business {$businessId}) / {$branch->name} (branch {$branchId})");
        $this->line(count($items).' sample product templates; price ₹0, inactive, no orders.');
        if ($this->option('dry-run')) {
            $this->info('Dry run: no database changes.');
            return self::SUCCESS;
        }
        $created = DB::transaction(function () use ($items, $businessId, $branchId): int {
            $created = 0;
            foreach ($items as [$name, $category]) {
                $exists = DB::table('sublimation_products')
                    ->where('business_id', $businessId)->where('branch_id', $branchId)
                    ->where('name', $name)->exists();
                if ($exists) {
                    continue;
                }
                $id = DB::table('sublimation_products')->insertGetId([
                    'business_id' => $businessId, 'branch_id' => $branchId,
                    'name' => $name, 'category' => $category,
                    'production_notes' => 'Sample template. Set actual material/printing/packing cost and sale price, then activate. Check compatible blanks and equipment before printing.',
                    'blank_cost_paise' => 0, 'print_cost_paise' => 0,
                    'packing_cost_paise' => 0, 'other_cost_paise' => 0,
                    'sale_price_paise' => 0, 'is_active' => false,
                    'created_at' => now(), 'updated_at' => now(),
                ]);
                DB::table('audit_logs')->insert([
                    'actor_user_id' => null,
                    'action' => 'sublimation.product.sample_seeded',
                    'entity_type' => 'sublimation_product', 'entity_id' => $id,
                    'new_values' => json_encode([
                        'business_id' => $businessId, 'branch_id' => $branchId,
                        'name' => $name, 'is_active' => false, 'sale_price_paise' => 0,
                        'origin' => 'explicit_cli_seed',
                    ]),
                    'created_at' => now(),
                ]);
                $created++;
            }
            return $created;
        });
        $this->info("Added {$created}; already existing ".(count($items)-$created).'. No existing products modified.');
        return self::SUCCESS;
    }
}
