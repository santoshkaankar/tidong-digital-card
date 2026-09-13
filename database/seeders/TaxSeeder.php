<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaxSeeder extends Seeder
{
    public function run(): void
    {
        $taxes = [
            [
                'tax_name' => 'Exempted (0%)',
                'tax_percentage' => 0.00,
                'cess_percentage' => 0.00,
                'tax_type' => 'percentage',
                'is_active' => true,
                'remark' => 'Tax Free / Exempted Items',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tax_name' => 'GST 5%',
                'tax_percentage' => 5.00,
                'cess_percentage' => 0.00,
                'tax_type' => 'percentage',
                'is_active' => true,
                'remark' => 'Standard Restaurant GST',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tax_name' => 'GST 12%',
                'tax_percentage' => 12.00,
                'cess_percentage' => 0.00,
                'tax_type' => 'percentage',
                'is_active' => true,
                'remark' => 'AC Restaurant GST',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tax_name' => 'GST 18%',
                'tax_percentage' => 18.00,
                'cess_percentage' => 0.00,
                'tax_type' => 'percentage',
                'is_active' => true,
                'remark' => 'Outdoor Catering / Standard Services',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tax_name' => 'GST 28%',
                'tax_percentage' => 28.00,
                'cess_percentage' => 0.00,
                'tax_type' => 'percentage',
                'is_active' => true,
                'remark' => 'Luxury / High-end Items',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($taxes as $tax) {
            DB::table('taxes')->updateOrInsert(
                ['tax_percentage' => $tax['tax_percentage']],
                $tax
            );
        }
    }
}