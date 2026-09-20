<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShoppingCatalogSeeder extends Seeder
{
    public function run()
    {
        // 1. Seed Units (UOM) safely
        $units = ['pcs', 'kg', 'g', 'L', 'ml', 'packet', 'box', 'meter'];
        foreach ($units as $u) {
            $exists = DB::table('units')->where('short_name', $u)->exists();
            if (!$exists) {
                DB::table('units')->insert([
                    'name' => ucfirst($u),
                    'short_name' => $u,
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }

        // 2. Real Brands Master safely
        $brands = [
            'HUL' => ['Surf Excel', 'Dove', 'Wheel', 'Lux', 'Rin', 'Lifebuoy', 'Bru', 'Kissan'],
            'P&G' => ['Tide', 'Head & Shoulders', 'Pantene', 'Gillette', 'Ariel', 'Oral-B', 'Vicks'],
            'ITC' => ['Aashirvaad', 'Sunfeast', 'Bingo', 'Yippee', 'Fiama', 'Classmate', 'Dark Fantasy'],
            'Nestle' => ['Maggi', 'KitKat', 'Nescafe', 'Milkybar', 'Everyday', 'Cerelac'],
            'Britannia' => ['Good Day', 'Marie Gold', 'Tiger', 'Bourbon', 'Milk Bikis', 'NutriChoice'],
            'Fashion' => ['Raymond', 'Levi\'s', 'Allen Solly', 'Van Heusen', 'Peter England', 'Biba', 'FabIndia']
        ];

        foreach ($brands as $group => $brandList) {
            foreach ($brandList as $bName) {
                $bExists = DB::table('brands')->where('brand_name', $bName)->exists();
                if (!$bExists) {
                    DB::table('brands')->insert([
                        'brand_name' => $bName,
                        'logo' => strtolower(str_replace([' ', "'"], '_', $bName)) . '_logo.jpg',
                        'status' => 'active',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
        }

        // 3. Categories & Massive Real Product Generation
        $catalogStructure = [
            'Grocery & Staples' => [
                'Atta, Rice & Dal' => ['Chakki Atta', 'Basmati Rice', 'Moong Dal', 'Toor Dal', 'Sugar', 'Salt', 'Maida', 'Poha'],
                'Edible Oils & Ghee' => ['Sunflower Oil', 'Mustard Oil', 'Groundnut Oil', 'Desi Ghee', 'Olive Oil', 'Soybean Oil'],
                'Spices & Masalas' => ['Turmeric Powder', 'Red Chilli Powder', 'Coriander Powder', 'Garam Masala', 'Jeera', 'Black Pepper']
            ],
            'Personal & Household Care' => [
                'Laundry & Detergent' => ['Washing Powder', 'Liquid Detergent', 'Fabric Softener', 'Dishwash Bar', 'Floor Cleaner'],
                'Skin & Hair Care' => ['Shampoo', 'Conditioner', 'Bath Soap', 'Face Wash', 'Body Lotion', 'Hair Oil', 'Toothpaste']
            ],
            'Snacks & Beverages' => [
                'Biscuits & Cookies' => ['Glucose Biscuits', 'Cream Biscuits', 'Digestive Cookies', 'Salted Crackers', 'Cookies Assortment'],
                'Instant Food & Tea' => ['Instant Noodles', 'Instant Soup', 'Green Tea', 'Instant Coffee Powder', 'Breakfast Cereal']
            ],
            'Clothing & Fashion' => [
                'Men Wear' => ['Cotton Casual T-Shirt', 'Formal Full Sleeve Shirt', 'Slim Fit Denim Jeans', 'Cotton Trouser', 'Trackpants'],
                'Women Ethnic Wear' => ['Designer Cotton Kurti', 'Printed Silk Saree', 'Anarkali Suit Set', 'Churidar Leggings', 'Party Wear Lehenga']
            ]
        ];

        foreach ($catalogStructure as $mainCat => $subCategories) {
            // Level 1 Category
            $catRecord = DB::table('item_categories')->where('name', $mainCat)->whereNull('parent_id')->first();
            if (!$catRecord) {
                $catId = DB::table('item_categories')->insertGetId([
                    'name' => $mainCat,
                    'parent_id' => null,
                    'level' => 1,
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $catId = $catRecord->id;
            }

            foreach ($subCategories as $subName => $itemTypes) {
                // Level 2 Sub Category
                $subRecord = DB::table('item_categories')->where('name', $subName)->where('parent_id', $catId)->first();
                if (!$subRecord) {
                    $subCatId = DB::table('item_categories')->insertGetId([
                        'name' => $subName,
                        'parent_id' => $catId,
                        'level' => 2,
                        'status' => 'active',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    $subCatId = $subRecord->id;
                }

                foreach ($itemTypes as $childName) {
                    // Level 3 Child Category
                    $childRecord = DB::table('item_categories')->where('name', $childName)->where('parent_id', $subCatId)->first();
                    if (!$childRecord) {
                        $childCatId = DB::table('item_categories')->insertGetId([
                            'name' => $childName,
                            'parent_id' => $subCatId,
                            'level' => 3,
                            'status' => 'active',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    } else {
                        $childCatId = $childRecord->id;
                    }

                    $flatBrandsList = [];
                    foreach ($brands as $bGroup) {
                        $flatBrandsList = array_merge($flatBrandsList, $bGroup);
                    }

                    for ($i = 1; $i <= 25; $i++) {
                        $brandName = $flatBrandsList[array_rand($flatBrandsList)];
                        $itemName = "$brandName $childName Variant $i";
                        $skuCode = strtoupper(substr(str_replace(' ', '', $brandName), 0, 3)) . '-' . strtoupper(substr(str_replace(' ', '', $childName), 0, 4)) . '-' . $i . '-' . rand(1000, 9999);

                        $itemExists = DB::table('global_items')->where('sku', $skuCode)->exists();
                        if ($itemExists) {
                            continue;
                        }

                        $globalItemId = DB::table('global_items')->insertGetId([
                            'category' => $mainCat,
                            'sub_category_id' => $subCatId,
                            'child_category_id' => $childCatId,
                            'item_name' => $itemName,
                            'brand_name' => $brandName,
                            'product_summary' => "High quality $childName manufactured by $brandName for daily consumer needs.",
                            'description' => "100% genuine and certified product from $brandName. Best suited for households and retail distribution.",
                            'sku' => $skuCode,
                            'barcode' => '890' . rand(1000000000, 999999999),
                            'product_type' => 'variant',
                            'default_price' => rand(99, 1499),
                            'min_order_quantity' => 1,
                            'quantity_step_size' => 1,
                            'total_allowed_quantity' => 100,
                            'status' => 1,
                            'is_cancelable' => 1,
                            'is_returnable' => 1,
                            'returnable_days' => 7,
                            'is_inclusive_tax' => 1,
                            'tags' => strtolower("$brandName, $childName, $mainCat"),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        $variantOptions = ['Small Pack / 250g', 'Medium Pack / 500g', 'Family Pack / 1kg'];
                        if ($mainCat == 'Clothing & Fashion') {
                            $variantOptions = ['Small (S)', 'Medium (M)', 'Large (L)'];
                        }

                        foreach ($variantOptions as $vIndex => $vLabel) {
                            $mrpVal = rand(200, 2500);
                            $priceVal = $mrpVal - rand(20, 150);
                            $vSku = $skuCode . '-V' . ($vIndex + 1);

                            $vExists = DB::table('global_item_variants')->where('sku', $vSku)->exists();
                            if (!$vExists) {
                                DB::table('global_item_variants')->insert([
                                    'global_item_id' => $globalItemId,
                                    'sku' => $vSku,
                                    'barcode' => '891' . rand(100000000, 999999999),
                                    'color' => ($mainCat == 'Clothing & Fashion') ? ['Red', 'Blue', 'Black', 'White'][rand(0, 3)] : 'Standard',
                                    'size' => $vLabel,
                                    'variant_name' => "$itemName - $vLabel",
                                    'mrp' => $mrpVal,
                                    'price' => $priceVal,
                                    'image' => 'item_default.jpg',
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);
                            }
                        }
                    }
                }
            }
        }
    }
}