<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GlobalItemsSeeder extends Seeder
{
    public function run(): void
    {
        // Category wise default tax IDs map kar rahe hain
        $categoryTaxMapping = [
            'Chaat & Street Food' => 2,
            'Raita & Salads' => 2,
            'Thali & Tiffin Meals' => 2,
            'Mithai & Sweets' => 2,
            'Main Course - Veg' => 2,
            'Main Course - Non-Veg' => 2,
            'Starters & Snacks' => 2,
            'Breads / Roti' => 2,
            'Rice & Biryani' => 2,
            'Beverages' => 2,
        ];

        $categoriesWithItems = [
            'Chaat & Street Food' => [
                ['name' => 'Pani Puri / Gupchup (6 Pcs)', 'type' => 'veg', 'mrp' => 40, 'desc' => 'Crispy hollow puris filled with spicy tangy water'],
                ['name' => 'Dahi Papdi Chaat', 'type' => 'veg', 'mrp' => 80, 'desc' => 'Crunchy papdi topped with curd, chutneys and spices'],
                ['name' => 'Aloo Tikki Chaat', 'type' => 'veg', 'mrp' => 70, 'desc' => 'Crispy potato patties served with chole and chutneys'],
                ['name' => 'Samosa Chaat', 'type' => 'veg', 'mrp' => 80, 'desc' => 'Crushed samosa loaded with curd, tamarind and mint chutney'],
                ['name' => 'Raj Kachori', 'type' => 'veg', 'mrp' => 110, 'desc' => 'Large crisp kachori filled with sprouts, curd and sev'],
                ['name' => 'Sev Puri', 'type' => 'veg', 'mrp' => 60, 'desc' => 'Flat puris topped with potatoes, onions and fine sev'],
            ],
            'Raita & Salads' => [
                ['name' => 'Boondi Raita', 'type' => 'veg', 'mrp' => 70, 'desc' => 'Fresh whipped yogurt with crispy boondi and roasted cumin'],
                ['name' => 'Mix Veg Raita', 'type' => 'veg', 'mrp' => 80, 'desc' => 'Yogurt mixed with finely chopped cucumber, tomato and onion'],
                ['name' => 'Pineapple Raita', 'type' => 'veg', 'mrp' => 100, 'desc' => 'Sweet and tangy yogurt with juicy pineapple chunks'],
                ['name' => 'Cucumber Raita', 'type' => 'veg', 'mrp' => 70, 'desc' => 'Cool yogurt blended with grated cucumber'],
                ['name' => 'Green Salad', 'type' => 'veg', 'mrp' => 60, 'desc' => 'Fresh slices of cucumber, tomato, onion and carrot with lemon'],
            ],
            'Thali & Tiffin Meals' => [
                ['name' => 'Mini Office Tiffin (Veg)', 'type' => 'veg', 'mrp' => 120, 'desc' => '4 Tawa Roti, Dal, Daily Sabzi, Rice and Salad'],
                ['name' => 'Standard Veg Thali', 'type' => 'veg', 'mrp' => 180, 'desc' => 'Paneer Sabzi, Dal Makhani, 4 Roti/Naan, Jeera Rice, Raita, Sweet'],
                ['name' => 'Deluxe Maharaja Thali', 'type' => 'veg', 'mrp' => 280, 'desc' => 'Soup, 2 Veg Sabzi, Dal, Pulao, Butter Naan, Raita, Papad, 2 Sweets'],
                ['name' => 'Non-Veg Special Thali', 'type' => 'non-veg', 'mrp' => 320, 'desc' => 'Chicken Curry, Dal, Rice, 3 Roti, Salad and Sweet'],
                ['name' => 'Executive Thali', 'type' => 'veg', 'mrp' => 210, 'desc' => 'Special Paneer dish, Yellow Dal, 3 Butter Roti, Rice, Boondi Raita, Salad'],
            ],
            'Mithai & Sweets' => [
                ['name' => 'Gulab Jamun (2 Pcs)', 'type' => 'veg', 'mrp' => 60, 'desc' => 'Soft fried milk dumplings soaked in rose sugar syrup'],
                ['name' => 'Rasgulla (2 Pcs)', 'type' => 'veg', 'mrp' => 60, 'desc' => 'Spongy cottage cheese balls in light sugar syrup'],
                ['name' => 'Rasmalai (2 Pcs)', 'type' => 'veg', 'mrp' => 90, 'desc' => 'Flattened paneer discs soaked in sweetened thick milk'],
                ['name' => 'Jalebi (Plate)', 'type' => 'veg', 'mrp' => 70, 'desc' => 'Crispy golden fried sweet swirls'],
                ['name' => 'Kaju Katli (100g)', 'type' => 'veg', 'mrp' => 150, 'desc' => 'Rich cashew fudge diamond shaped sweet'],
                ['name' => 'Gajar Ka Halwa', 'type' => 'veg', 'mrp' => 100, 'desc' => 'Warm winter dessert made with carrots, milk and dry fruits'],
                ['name' => 'Moong Dal Halwa', 'type' => 'veg', 'mrp' => 120, 'desc' => 'Rich and traditional lentil pudding cooked in pure ghee'],
            ],
            'Main Course - Veg' => [
                ['name' => 'Paneer Butter Masala (Half)', 'type' => 'veg', 'mrp' => 150, 'desc' => 'Half portion rich tomato and butter gravy paneer'],
                ['name' => 'Paneer Butter Masala (Full)', 'type' => 'veg', 'mrp' => 260, 'desc' => 'Full portion rich tomato and butter gravy paneer'],
                ['name' => 'Kadhai Paneer (Half)', 'type' => 'veg', 'mrp' => 140, 'desc' => 'Half portion paneer tossed with capsicum and spices'],
                ['name' => 'Kadhai Paneer (Full)', 'type' => 'veg', 'mrp' => 250, 'desc' => 'Full portion paneer tossed with capsicum and spices'],
                ['name' => 'Dal Tadka (Half)', 'type' => 'veg', 'mrp' => 100, 'desc' => 'Half portion yellow lentils with garlic tempering'],
                ['name' => 'Dal Tadka (Full)', 'type' => 'veg', 'mrp' => 170, 'desc' => 'Full portion yellow lentils with garlic tempering'],
                ['name' => 'Dal Makhani (Half)', 'type' => 'veg', 'mrp' => 130, 'desc' => 'Half portion slow-cooked black lentils with butter and cream'],
                ['name' => 'Dal Makhani (Full)', 'type' => 'veg', 'mrp' => 220, 'desc' => 'Full portion slow-cooked black lentils with butter and cream'],
            ],
            'Main Course - Non-Veg' => [
                ['name' => 'Butter Chicken (Half)', 'type' => 'non-veg', 'mrp' => 200, 'desc' => 'Half portion tender chicken in rich tomato butter gravy'],
                ['name' => 'Butter Chicken (Full)', 'type' => 'non-veg', 'mrp' => 340, 'desc' => 'Full portion tender chicken in rich tomato butter gravy'],
                ['name' => 'Chicken Curry (Half)', 'type' => 'non-veg', 'mrp' => 180, 'desc' => 'Half portion homestyle spiced chicken gravy'],
                ['name' => 'Chicken Curry (Full)', 'type' => 'non-veg', 'mrp' => 300, 'desc' => 'Full portion homestyle spiced chicken gravy'],
            ],
            'Starters & Snacks' => [
                ['name' => 'Paneer Tikka', 'type' => 'veg', 'mrp' => 240, 'desc' => 'Marinated paneer cubes grilled in tandoor'],
                ['name' => 'Veg Manchurian', 'type' => 'veg', 'mrp' => 180, 'desc' => 'Crispy vegetable balls tossed in soy garlic sauce'],
                ['name' => 'French Fries', 'type' => 'veg', 'mrp' => 120, 'desc' => 'Classic salted potato fries'],
            ],
            'Breads / Roti' => [
                ['name' => 'Tandoori Roti Plain', 'type' => 'veg', 'mrp' => 15, 'desc' => 'Whole wheat flatbread cooked in tandoor'],
                ['name' => 'Tandoori Butter Roti', 'type' => 'veg', 'mrp' => 20, 'desc' => 'Tandoori roti topped with butter'],
                ['name' => 'Butter Naan', 'type' => 'veg', 'mrp' => 50, 'desc' => 'Leavened bread topped with melted butter'],
                ['name' => 'Garlic Naan', 'type' => 'veg', 'mrp' => 65, 'desc' => 'Naan topped with chopped garlic and butter'],
            ],
            'Rice & Biryani' => [
                ['name' => 'Plain Steam Rice', 'type' => 'veg', 'mrp' => 110, 'desc' => 'Steamed long grain basmati rice'],
                ['name' => 'Jeera Rice', 'type' => 'veg', 'mrp' => 130, 'desc' => 'Basmati rice tempered with cumin seeds'],
                ['name' => 'Veg Dum Biryani', 'type' => 'veg', 'mrp' => 220, 'desc' => 'Aromatic rice layer cooked with marinated veggies'],
                ['name' => 'Chicken Biryani', 'type' => 'non-veg', 'mrp' => 280, 'desc' => 'Hyderabadi style layered chicken biryani'],
            ],
            'Beverages' => [
                ['name' => 'Masala Chai', 'type' => 'veg', 'mrp' => 30, 'desc' => 'Indian spiced milk tea'],
                ['name' => 'Cold Coffee', 'type' => 'veg', 'mrp' => 110, 'desc' => 'Chilled coffee blended with ice cream'],
                ['name' => 'Sweet Lassi', 'type' => 'veg', 'mrp' => 70, 'desc' => 'Traditional Punjabi sweetened yogurt drink'],
            ]
        ];

        foreach ($categoriesWithItems as $categoryName => $items) {
            DB::table('item_categories')->updateOrInsert(
                ['name' => $categoryName],
                [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            $assignedTaxId = $categoryTaxMapping[$categoryName] ?? 2;

            foreach ($items as $item) {
                DB::table('global_items')->updateOrInsert(
                    ['item_name' => $item['name'], 'category' => $categoryName],
                    [
                        'food_type' => $item['type'],
                        'mrp' => $item['mrp'],
                        'default_price' => $item['mrp'],
                        'tax_id' => $assignedTaxId,
                        'description' => $item['desc'],
                        'status' => 'approved',
                        'is_approved' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
}