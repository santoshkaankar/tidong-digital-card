<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ItemCategory;

class GlobalItemsSeeder extends Seeder
{
    public function run(): void
    {
        $categoriesWithItems = [
            'Soups' => [
                ['name' => 'Tomato Soup', 'type' => 'veg', 'mrp' => 120, 'desc' => 'Classic creamy tomato soup served with croutons'],
                ['name' => 'Hot & Sour Soup', 'type' => 'veg', 'mrp' => 130, 'desc' => 'Spicy and tangy vegetable soup'],
                ['name' => 'Sweet Corn Soup', 'type' => 'veg', 'mrp' => 130, 'desc' => 'Mild sweet corn vegetable soup'],
                ['name' => 'Manchow Soup', 'type' => 'veg', 'mrp' => 140, 'desc' => 'Indo-Chinese soup served with crispy noodles'],
                ['name' => 'Chicken Manchow Soup', 'type' => 'non-veg', 'mrp' => 160, 'desc' => 'Chicken soup topped with crispy noodles'],
            ],
            'Starters & Snacks' => [
                ['name' => 'Paneer Tikka', 'type' => 'veg', 'mrp' => 240, 'desc' => 'Marinated paneer cubes grilled in tandoor'],
                ['name' => 'Veg Hara Bhara Kabab', 'type' => 'veg', 'mrp' => 200, 'desc' => 'Crispy spinach and green pea patties'],
                ['name' => 'Crispy Corn', 'type' => 'veg', 'mrp' => 180, 'desc' => 'Fried sweet corn tossed with spices'],
                ['name' => 'French Fries', 'type' => 'veg', 'mrp' => 120, 'desc' => 'Classic salted potato fries'],
                ['name' => 'Peri Peri Fries', 'type' => 'veg', 'mrp' => 140, 'desc' => 'Fries tossed in spicy peri peri seasoning'],
                ['name' => 'Spring Roll', 'type' => 'veg', 'mrp' => 170, 'desc' => 'Crispy rolls filled with shredded veggies'],
                ['name' => 'Chicken Tikka', 'type' => 'non-veg', 'mrp' => 280, 'desc' => 'Boneless chicken marinated and cooked in tandoor'],
                ['name' => 'Tandoori Chicken (Half)', 'type' => 'non-veg', 'mrp' => 320, 'desc' => 'Traditional roasted chicken'],
            ],
            'Chinese' => [
                ['name' => 'Veg Noodles / Chowmein', 'type' => 'veg', 'mrp' => 150, 'desc' => 'Stir-fried noodles with fresh vegetables'],
                ['name' => 'Hakka Noodles', 'type' => 'veg', 'mrp' => 160, 'desc' => 'Classic Indo-Chinese tossed noodles'],
                ['name' => 'Veg Manchurian Dry', 'type' => 'veg', 'mrp' => 180, 'desc' => 'Crispy veggie balls in tangy sauce'],
                ['name' => 'Veg Manchurian Gravy', 'type' => 'veg', 'mrp' => 190, 'desc' => 'Veggie balls in rich Manchurian gravy'],
                ['name' => 'Chilli Paneer Dry', 'type' => 'veg', 'mrp' => 220, 'desc' => 'Fried paneer tossed with capsicum and chili sauce'],
                ['name' => 'Chicken Noodles', 'type' => 'non-veg', 'mrp' => 200, 'desc' => 'Stir-fried noodles with chicken strips'],
                ['name' => 'Chilli Chicken', 'type' => 'non-veg', 'mrp' => 260, 'desc' => 'Boneless chicken tossed in soy-chili sauce'],
            ],
            'South Indian' => [
                ['name' => 'Plain Dosa', 'type' => 'veg', 'mrp' => 90, 'desc' => 'Crispy rice crepe served with sambar and chutney'],
                ['name' => 'Masala Dosa', 'type' => 'veg', 'mrp' => 130, 'desc' => 'Crispy crepe filled with spiced potato masala'],
                ['name' => 'Butter Masala Dosa', 'type' => 'veg', 'mrp' => 150, 'desc' => 'Rich butter coated masala dosa'],
                ['name' => 'Idli Sambar (2 Pcs)', 'type' => 'veg', 'mrp' => 80, 'desc' => 'Steamed rice cakes served with sambar'],
                ['name' => 'Vada Sambar (2 Pcs)', 'type' => 'veg', 'mrp' => 90, 'desc' => 'Crispy lentil donuts served with sambar'],
                ['name' => 'Uttapam (Onion/Tomato)', 'type' => 'veg', 'mrp' => 130, 'desc' => 'Thick rice pancake topped with onions and tomatoes'],
            ],
            'Main Course - Veg' => [
                ['name' => 'Paneer Butter Masala', 'type' => 'veg', 'mrp' => 260, 'desc' => 'Paneer cooked in rich tomato and butter gravy'],
                ['name' => 'Kadhai Paneer', 'type' => 'veg', 'mrp' => 250, 'desc' => 'Paneer tossed with bell peppers and fresh spices'],
                ['name' => 'Shahi Paneer', 'type' => 'veg', 'mrp' => 270, 'desc' => 'Paneer in creamy cashew and onion gravy'],
                ['name' => 'Matar Paneer', 'type' => 'veg', 'mrp' => 230, 'desc' => 'Cottage cheese and green peas curry'],
                ['name' => 'Dal Tadka', 'type' => 'veg', 'mrp' => 170, 'desc' => 'Yellow lentils tempered with garlic and cumin'],
                ['name' => 'Dal Makhani', 'type' => 'veg', 'mrp' => 220, 'desc' => 'Slow-cooked black lentils creamed with butter'],
                ['name' => 'Mix Vegetable', 'type' => 'veg', 'mrp' => 200, 'desc' => 'Assorted seasonal vegetables curry'],
                ['name' => 'Chana Masala', 'type' => 'veg', 'mrp' => 190, 'desc' => 'Spiced chickpea gravy cooked Punjabi style'],
                ['name' => 'Aloo Gobi', 'type' => 'veg', 'mrp' => 170, 'desc' => 'Potato and cauliflower dry curry'],
            ],
            'Main Course - Non-Veg' => [
                ['name' => 'Butter Chicken', 'type' => 'non-veg', 'mrp' => 340, 'desc' => 'Tender chicken in rich tomato butter gravy'],
                ['name' => 'Chicken Curry', 'type' => 'non-veg', 'mrp' => 300, 'desc' => 'Homestyle spiced chicken gravy'],
                ['name' => 'Kadhai Chicken', 'type' => 'non-veg', 'mrp' => 320, 'desc' => 'Chicken cooked with whole spices and capsicum'],
                ['name' => 'Mutton Curry', 'type' => 'non-veg', 'mrp' => 420, 'desc' => 'Slow cooked tender mutton curry'],
            ],
            'Breads / Roti' => [
                ['name' => 'Tandoori Roti Plain', 'type' => 'veg', 'mrp' => 15, 'desc' => 'Whole wheat flatbread cooked in tandoor'],
                ['name' => 'Tandoori Butter Roti', 'type' => 'veg', 'mrp' => 20, 'desc' => 'Tandoori roti topped with butter'],
                ['name' => 'Plain Naan', 'type' => 'veg', 'mrp' => 40, 'desc' => 'Leavened fine flour bread'],
                ['name' => 'Butter Naan', 'type' => 'veg', 'mrp' => 50, 'desc' => 'Leavened bread topped with melted butter'],
                ['name' => 'Garlic Naan', 'type' => 'veg', 'mrp' => 65, 'desc' => 'Naan topped with chopped garlic and butter'],
                ['name' => 'Lachha Paratha', 'type' => 'veg', 'mrp' => 45, 'desc' => 'Layered crispy whole wheat bread'],
                ['name' => 'Stuffed Aloo Paratha', 'type' => 'veg', 'mrp' => 70, 'desc' => 'Paratha stuffed with spiced potato filling'],
            ],
            'Rice & Biryani' => [
                ['name' => 'Plain Steam Rice', 'type' => 'veg', 'mrp' => 110, 'desc' => 'Steamed long grain basmati rice'],
                ['name' => 'Jeera Rice', 'type' => 'veg', 'mrp' => 130, 'desc' => 'Basmati rice tempered with cumin seeds'],
                ['name' => 'Veg Fried Rice', 'type' => 'veg', 'mrp' => 160, 'desc' => 'Rice tossed with chopped veggies'],
                ['name' => 'Veg Pulao', 'type' => 'veg', 'mrp' => 180, 'desc' => 'Rice cooked with vegetables and mild spices'],
                ['name' => 'Veg Dum Biryani', 'type' => 'veg', 'mrp' => 220, 'desc' => 'Aromatic rice layer cooked with marinated veggies'],
                ['name' => 'Chicken Biryani', 'type' => 'non-veg', 'mrp' => 280, 'desc' => 'Hyderabadi style layered chicken biryani'],
            ],
            'Fast Food & Burgers' => [
                ['name' => 'Veg Burger', 'type' => 'veg', 'mrp' => 80, 'desc' => 'Classic potato patty burger with mayo'],
                ['name' => 'Veg Cheese Burger', 'type' => 'veg', 'mrp' => 100, 'desc' => 'Veggie burger loaded with cheese slice'],
                ['name' => 'Chicken Burger', 'type' => 'non-veg', 'mrp' => 120, 'desc' => 'Crispy chicken patty burger'],
                ['name' => 'Veg Grilled Sandwich', 'type' => 'veg', 'mrp' => 110, 'desc' => 'Toasted sandwich filled with veggies and cheese'],
                ['name' => 'Paneer Tikka Sandwich', 'type' => 'veg', 'mrp' => 140, 'desc' => 'Grilled sandwich filled with paneer tikka'],
            ],
            'Pizza' => [
                ['name' => 'Margherita Pizza', 'type' => 'veg', 'mrp' => 190, 'desc' => 'Classic cheese and tomato sauce pizza'],
                ['name' => 'Farmhouse Pizza', 'type' => 'veg', 'mrp' => 260, 'desc' => 'Topped with capsicum, onion, tomato, mushroom'],
                ['name' => 'Paneer Special Pizza', 'type' => 'veg', 'mrp' => 290, 'desc' => 'Loaded with spicy paneer cubes and veggies'],
            ],
            'Beverages & Drinks' => [
                ['name' => 'Masala Chai', 'type' => 'veg', 'mrp' => 30, 'desc' => 'Indian spiced milk tea'],
                ['name' => 'Hot Coffee', 'type' => 'veg', 'mrp' => 50, 'desc' => 'Brewed milk coffee'],
                ['name' => 'Cold Coffee', 'type' => 'veg', 'mrp' => 110, 'desc' => 'Chilled coffee blended with ice cream'],
                ['name' => 'Sweet Lassi', 'type' => 'veg', 'mrp' => 70, 'desc' => 'Traditional Punjabi sweetened yogurt drink'],
                ['name' => 'Fresh Lime Soda', 'type' => 'veg', 'mrp' => 60, 'desc' => 'Refreshing lime drink with soda'],
                ['name' => 'Mineral Water (1L)', 'type' => 'veg', 'mrp' => 20, 'desc' => 'Packaged drinking water'],
            ],
            'Desserts & Sweets' => [
                ['name' => 'Gulab Jamun (2 Pcs)', 'type' => 'veg', 'mrp' => 60, 'desc' => 'Classic sweet fried milk solids in sugar syrup'],
                ['name' => 'Rasgulla (2 Pcs)', 'type' => 'veg', 'mrp' => 60, 'desc' => 'Soft cottage cheese balls in sugar syrup'],
                ['name' => 'Vanilla Ice Cream', 'type' => 'veg', 'mrp' => 50, 'desc' => 'Classic vanilla flavor scoop'],
                ['name' => 'Chocolate Ice Cream', 'type' => 'veg', 'mrp' => 70, 'desc' => 'Rich chocolate flavor scoop'],
            ],
        ];

        foreach ($categoriesWithItems as $categoryName => $items) {
            // 1. Fill `item_categories` table
            DB::table('item_categories')->updateOrInsert(
                ['name' => $categoryName],
                [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            // 2. Fill `global_items` table
            foreach ($items as $item) {
                DB::table('global_items')->updateOrInsert(
                    ['item_name' => $item['name'], 'category' => $categoryName],
                    [
                        'food_type' => $item['type'],
                        'mrp' => $item['mrp'],
                        'default_price' => $item['mrp'],
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