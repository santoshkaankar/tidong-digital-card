<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AffiliateStageSeeder extends Seeder
{
    public function run(): void
    {
        // Purana data clear karke fresh seed karne ke liye (Optional)
        DB::table('affiliate_stages')->truncate();

        $stages = [
            [
                'stage_no' => 1,
                'stage_name' => 'Global Distributor',
                'leg_a_count' => 1,
                'leg_b_count' => 2,
                'incentive_amount' => 500.00,
            ],
            [
                'stage_no' => 2,
                'stage_name' => 'Global Senior Distributor',
                'leg_a_count' => 2,
                'leg_b_count' => 4,
                'incentive_amount' => 1000.00,
            ],
            [
                'stage_no' => 3,
                'stage_name' => 'Global Junior Pearl Distributor',
                'leg_a_count' => 6,
                'leg_b_count' => 12,
                'incentive_amount' => 2000.00,
            ],
            [
                'stage_no' => 4,
                'stage_name' => 'Global Assistant Pearl Distributor',
                'leg_a_count' => 44,
                'leg_b_count' => 66,
                'incentive_amount' => 8500.00,
            ],
            [
                'stage_no' => 5,
                'stage_name' => 'Global Branch Pearl Distributor',
                'leg_a_count' => 86,
                'leg_b_count' => 129,
                'incentive_amount' => 18000.00,
            ],
            [
                'stage_no' => 6,
                'stage_name' => 'Global Pearl Distributor',
                'leg_a_count' => 176,
                'leg_b_count' => 264,
                'incentive_amount' => 28000.00,
            ],
            [
                'stage_no' => 7,
                'stage_name' => 'Global Junior Rubey Distributor',
                'leg_a_count' => 356,
                'leg_b_count' => 534,
                'incentive_amount' => 45000.00,
            ],
            [
                'stage_no' => 8,
                'stage_name' => 'Global Rubey Distributor',
                'leg_a_count' => 988,
                'leg_b_count' => 1482,
                'incentive_amount' => 87000.00,
            ],
            [
                'stage_no' => 9,
                'stage_name' => 'Global Junior Emerald Distributor',
                'leg_a_count' => 2600,
                'leg_b_count' => 3900,
                'incentive_amount' => 120000.00,
            ],
            [
                'stage_no' => 10,
                'stage_name' => 'Global Assistant Emerald Distributor',
                'leg_a_count' => 4500,
                'leg_b_count' => 6750,
                'incentive_amount' => 180000.00,
            ],
            [
                'stage_no' => 11,
                'stage_name' => 'Global Emerald Distributor',
                'leg_a_count' => 6800,
                'leg_b_count' => 10200,
                'incentive_amount' => 250000.00,
            ],
            [
                'stage_no' => 12,
                'stage_name' => 'Global Junior Dimond Distributor',
                'leg_a_count' => 14500,
                'leg_b_count' => 21750,
                'incentive_amount' => 300000.00,
            ],
            [
                'stage_no' => 13,
                'stage_name' => 'Global Assistant Dimond Distributor',
                'leg_a_count' => 22000,
                'leg_b_count' => 33000,
                'incentive_amount' => 500000.00,
            ],
            [
                'stage_no' => 14,
                'stage_name' => 'Global Dimond Distributor',
                'leg_a_count' => 38000,
                'leg_b_count' => 57000,
                'incentive_amount' => 3000000.00,
            ],
        ];

        foreach ($stages as $stage) {
            DB::table('affiliate_stages')->updateOrInsert(
                ['stage_no' => $stage['stage_no']],
                [
                    'stage_name'       => $stage['stage_name'],
                    'leg_a_count'      => $stage['leg_a_count'],
                    'leg_b_count'      => $stage['leg_b_count'],
                    'incentive_amount' => $stage['incentive_amount'],
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]
            );
        }
    }
}