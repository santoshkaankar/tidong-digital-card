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
            'Soups' => 2,
            'Starters & Snacks' => 2,
            'Main Course - Veg' => 2,
            'Main Course - Non-Veg' => 2,
            'Breads / Roti' => 2,
            'Rice & Biryani' => 2,
            'South Indian' => 2,
            'Fast Food & Pizza' => 2,
            'Beverages' => 2,
            'Desserts & Sweets' => 2,
            'Emporium - Handicrafts & Souvenirs' => 3,
            'Emporium - Clothing & Apparel' => 3,
            'Emporium - Leather Goods' => 4,
            'Emporium - Jewelry & Accessories' => 4,
            'Emporium - Spices & Teas' => 2,
            'Emporium - Home Decor' => 3,
        ];

        $categoriesWithItems = [
            'Soups' => [
                ['name' => 'Tomato Soup', 'type' => 'veg', 'mrp' => 120, 'desc' => 'Classic creamy tomato soup served with croutons', 'image' => 'tomato_soup.jpg'],
                ['name' => 'Hot & Sour Soup', 'type' => 'veg', 'mrp' => 130, 'desc' => 'Spicy and tangy vegetable soup', 'image' => 'hot_sour_soup.jpg'],
                ['name' => 'Sweet Corn Soup', 'type' => 'veg', 'mrp' => 130, 'desc' => 'Mild sweet corn vegetable soup', 'image' => 'sweet_corn_soup.jpg'],
                ['name' => 'Manchow Soup', 'type' => 'veg', 'mrp' => 140, 'desc' => 'Indo-Chinese soup served with crispy noodles', 'image' => 'manchow_soup.jpg'],
                ['name' => 'Lemon Coriander Soup', 'type' => 'veg', 'mrp' => 135, 'desc' => 'Healthy clear soup with fresh coriander and lemon', 'image' => 'lemon_coriander.jpg'],
                ['name' => 'Chicken Manchow Soup', 'type' => 'non-veg', 'mrp' => 160, 'desc' => 'Chicken soup topped with crispy noodles', 'image' => 'chicken_manchow.jpg'],
                ['name' => 'Chicken Hot & Sour Soup', 'type' => 'non-veg', 'mrp' => 165, 'desc' => 'Tangy and spicy chicken soup', 'image' => 'chicken_hot_sour.jpg'],
            ],
            'Starters & Snacks' => [
                ['name' => 'Paneer Tikka', 'type' => 'veg', 'mrp' => 240, 'desc' => 'Marinated paneer cubes grilled in tandoor', 'image' => 'paneer_tikka.jpg'],
                ['name' => 'Paneer Malai Tikka', 'type' => 'veg', 'mrp' => 260, 'desc' => 'Creamy paneer cubes marinated with cashew and cream', 'image' => 'paneer_malai.jpg'],
                ['name' => 'Veg Hara Bhara Kabab', 'type' => 'veg', 'mrp' => 200, 'desc' => 'Crispy spinach and green pea patties', 'image' => 'hara_bhara.jpg'],
                ['name' => 'Crispy Corn', 'type' => 'veg', 'mrp' => 180, 'desc' => 'Fried sweet corn tossed with spices', 'image' => 'crispy_corn.jpg'],
                ['name' => 'French Fries', 'type' => 'veg', 'mrp' => 120, 'desc' => 'Classic salted potato fries', 'image' => 'french_fries.jpg'],
                ['name' => 'Peri Peri Fries', 'type' => 'veg', 'mrp' => 140, 'desc' => 'Fries tossed in spicy peri peri seasoning', 'image' => 'peri_peri_fries.jpg'],
                ['name' => 'Cheese Corn Balls', 'type' => 'veg', 'mrp' => 220, 'desc' => 'Deep fried balls filled with molten cheese and corn', 'image' => 'cheese_corn_balls.jpg'],
                ['name' => 'Honey Chilli Potato', 'type' => 'veg', 'mrp' => 190, 'desc' => 'Crispy potatoes tossed in sweet chili sesame sauce', 'image' => 'honey_chilli_potato.jpg'],
                ['name' => 'Chicken Tikka', 'type' => 'non-veg', 'mrp' => 280, 'desc' => 'Boneless chicken marinated and cooked in tandoor', 'image' => 'chicken_tikka.jpg'],
                ['name' => 'Tandoori Chicken (Half)', 'type' => 'non-veg', 'mrp' => 320, 'desc' => 'Traditional roasted chicken', 'image' => 'tandoori_chicken_half.jpg'],
                ['name' => 'Chicken Seekh Kebab', 'type' => 'non-veg', 'mrp' => 290, 'desc' => 'Minced chicken skewers cooked over charcoal', 'image' => 'chicken_seekh.jpg'],
                ['name' => 'Amritsari Fish Fry', 'type' => 'non-veg', 'mrp' => 380, 'desc' => 'Crispy gram flour batter fried fish', 'image' => 'fish_fry.jpg'],
            ],
            'Main Course - Veg' => [
                ['name' => 'Paneer Butter Masala', 'type' => 'veg', 'mrp' => 260, 'desc' => 'Paneer cooked in rich tomato and butter gravy', 'image' => 'paneer_butter_masala.jpg'],
                ['name' => 'Kadhai Paneer', 'type' => 'veg', 'mrp' => 250, 'desc' => 'Paneer tossed with bell peppers and fresh spices', 'image' => 'kadhai_paneer.jpg'],
                ['name' => 'Shahi Paneer', 'type' => 'veg', 'mrp' => 270, 'desc' => 'Paneer in creamy cashew and onion gravy', 'image' => 'shahi_paneer.jpg'],
                ['name' => 'Palak Paneer', 'type' => 'veg', 'mrp' => 250, 'desc' => 'Paneer cubes cooked in smooth spinach puree', 'image' => 'palak_paneer.jpg'],
                ['name' => 'Dal Tadka', 'type' => 'veg', 'mrp' => 170, 'desc' => 'Yellow lentils tempered with garlic and cumin', 'image' => 'dal_tadka.jpg'],
                ['name' => 'Dal Makhani', 'type' => 'veg', 'mrp' => 220, 'desc' => 'Slow-cooked black lentils creamed with butter', 'image' => 'dal_makhani.jpg'],
                ['name' => 'Mix Vegetable', 'type' => 'veg', 'mrp' => 200, 'desc' => 'Assorted seasonal vegetables curry', 'image' => 'mix_veg.jpg'],
                ['name' => 'Malai Kofta', 'type' => 'veg', 'mrp' => 280, 'desc' => 'Soft cottage cheese balls in creamy rich gravy', 'image' => 'malai_kofta.jpg'],
            ],
            'Main Course - Non-Veg' => [
                ['name' => 'Butter Chicken', 'type' => 'non-veg', 'mrp' => 340, 'desc' => 'Tender chicken in rich tomato butter gravy', 'image' => 'butter_chicken.jpg'],
                ['name' => 'Chicken Curry', 'type' => 'non-veg', 'mrp' => 300, 'desc' => 'Homestyle spiced chicken gravy', 'image' => 'chicken_curry.jpg'],
                ['name' => 'Kadhai Chicken', 'type' => 'non-veg', 'mrp' => 320, 'desc' => 'Chicken cooked with whole spices and capsicum', 'image' => 'kadhai_chicken.jpg'],
                ['name' => 'Handi Chicken', 'type' => 'non-veg', 'mrp' => 360, 'desc' => 'Chicken cooked slowly in traditional earthenware vessel', 'image' => 'handi_chicken.jpg'],
                ['name' => 'Mutton Curry', 'type' => 'non-veg', 'mrp' => 420, 'desc' => 'Slow cooked tender mutton curry', 'image' => 'mutton_curry.jpg'],
                ['name' => 'Mutton Rogan Josh', 'type' => 'non-veg', 'mrp' => 460, 'desc' => 'Kashmiri style mutton cooked in aromatic spices', 'image' => 'mutton_rogan_josh.jpg'],
            ],
            'Breads / Roti' => [
                ['name' => 'Tandoori Roti Plain', 'type' => 'veg', 'mrp' => 15, 'desc' => 'Whole wheat flatbread cooked in tandoor', 'image' => 'tandoori_roti.jpg'],
                ['name' => 'Tandoori Butter Roti', 'type' => 'veg', 'mrp' => 20, 'desc' => 'Tandoori roti topped with butter', 'image' => 'butter_roti.jpg'],
                ['name' => 'Plain Naan', 'type' => 'veg', 'mrp' => 40, 'desc' => 'Leavened fine flour bread', 'image' => 'plain_naan.jpg'],
                ['name' => 'Butter Naan', 'type' => 'veg', 'mrp' => 50, 'desc' => 'Leavened bread topped with melted butter', 'image' => 'butter_naan.jpg'],
                ['name' => 'Garlic Naan', 'type' => 'veg', 'mrp' => 65, 'desc' => 'Naan topped with chopped garlic and butter', 'image' => 'garlic_naan.jpg'],
                ['name' => 'Cheese Garlic Naan', 'type' => 'veg', 'mrp' => 90, 'desc' => 'Naan stuffed with cheese and garlic', 'image' => 'cheese_garlic_naan.jpg'],
                ['name' => 'Lachha Paratha', 'type' => 'veg', 'mrp' => 45, 'desc' => 'Layered crispy whole wheat bread', 'image' => 'lachha_paratha.jpg'],
            ],
            'Rice & Biryani' => [
                ['name' => 'Plain Steam Rice', 'type' => 'veg', 'mrp' => 110, 'desc' => 'Steamed long grain basmati rice', 'image' => 'steam_rice.jpg'],
                ['name' => 'Jeera Rice', 'type' => 'veg', 'mrp' => 130, 'desc' => 'Basmati rice tempered with cumin seeds', 'image' => 'jeera_rice.jpg'],
                ['name' => 'Veg Fried Rice', 'type' => 'veg', 'mrp' => 160, 'desc' => 'Rice tossed with chopped veggies', 'image' => 'veg_fried_rice.jpg'],
                ['name' => 'Veg Dum Biryani', 'type' => 'veg', 'mrp' => 220, 'desc' => 'Aromatic rice layer cooked with marinated veggies', 'image' => 'veg_biryani.jpg'],
                ['name' => 'Chicken Biryani', 'type' => 'non-veg', 'mrp' => 280, 'desc' => 'Hyderabadi style layered chicken biryani', 'image' => 'chicken_biryani.jpg'],
                ['name' => 'Mutton Biryani', 'type' => 'non-veg', 'mrp' => 380, 'desc' => 'Royal basmati rice cooked with tender mutton chunks', 'image' => 'mutton_biryani.jpg'],
            ],
            'South Indian' => [
                ['name' => 'Plain Dosa', 'type' => 'veg', 'mrp' => 90, 'desc' => 'Crispy rice crepe served with sambar and chutney', 'image' => 'plain_dosa.jpg'],
                ['name' => 'Masala Dosa', 'type' => 'veg', 'mrp' => 130, 'desc' => 'Crispy crepe filled with spiced potato masala', 'image' => 'masala_dosa.jpg'],
                ['name' => 'Butter Masala Dosa', 'type' => 'veg', 'mrp' => 150, 'desc' => 'Rich butter coated masala dosa', 'image' => 'butter_masala_dosa.jpg'],
                ['name' => 'Idli Sambar (2 Pcs)', 'type' => 'veg', 'mrp' => 80, 'desc' => 'Steamed rice cakes served with sambar', 'image' => 'idli_sambar.jpg'],
                ['name' => 'Vada Sambar (2 Pcs)', 'type' => 'veg', 'mrp' => 90, 'desc' => 'Crispy lentil donuts served with sambar', 'image' => 'vada_sambar.jpg'],
            ],
            'Fast Food & Pizza' => [
                ['name' => 'Veg Burger', 'type' => 'veg', 'mrp' => 80, 'desc' => 'Classic potato patty burger with mayo', 'image' => 'veg_burger.jpg'],
                ['name' => 'Veg Cheese Burger', 'type' => 'veg', 'mrp' => 100, 'desc' => 'Veggie burger loaded with cheese slice', 'image' => 'veg_cheese_burger.jpg'],
                ['name' => 'Chicken Burger', 'type' => 'non-veg', 'mrp' => 130, 'desc' => 'Crispy chicken patty burger', 'image' => 'chicken_burger.jpg'],
                ['name' => 'Margherita Pizza', 'type' => 'veg', 'mrp' => 190, 'desc' => 'Classic cheese and tomato sauce pizza', 'image' => 'margherita_pizza.jpg'],
                ['name' => 'Farmhouse Pizza', 'type' => 'veg', 'mrp' => 260, 'desc' => 'Topped with capsicum, onion, tomato, mushroom', 'image' => 'farmhouse_pizza.jpg'],
            ],
            'Beverages' => [
                ['name' => 'Masala Chai', 'type' => 'veg', 'mrp' => 30, 'desc' => 'Indian spiced milk tea', 'image' => 'masala_chai.jpg'],
                ['name' => 'Hot Coffee', 'type' => 'veg', 'mrp' => 50, 'desc' => 'Brewed milk coffee', 'image' => 'hot_coffee.jpg'],
                ['name' => 'Cold Coffee', 'type' => 'veg', 'mrp' => 110, 'desc' => 'Chilled coffee blended with ice cream', 'image' => 'cold_coffee.jpg'],
                ['name' => 'Sweet Lassi', 'type' => 'veg', 'mrp' => 70, 'desc' => 'Traditional Punjabi sweetened yogurt drink', 'image' => 'sweet_lassi.jpg'],
                ['name' => 'Mango Lassi', 'type' => 'veg', 'mrp' => 90, 'desc' => 'Smoothie made with yogurt and mango pulp', 'image' => 'mango_lassi.jpg'],
                ['name' => 'Mineral Water (1L)', 'type' => 'veg', 'mrp' => 20, 'desc' => 'Packaged drinking water', 'image' => 'water_bottle.jpg'],
            ],
            'Desserts & Sweets' => [
                ['name' => 'Gulab Jamun (2 Pcs)', 'type' => 'veg', 'mrp' => 60, 'desc' => 'Classic sweet fried milk solids in sugar syrup', 'image' => 'gulab_jamun.jpg'],
                ['name' => 'Rasmalai (2 Pcs)', 'type' => 'veg', 'mrp' => 90, 'desc' => 'Flattened paneer balls soaked in thickened milk', 'image' => 'rasmalai.jpg'],
                ['name' => 'Gajar Ka Halwa', 'type' => 'veg', 'mrp' => 100, 'desc' => 'Traditional carrot pudding made with milk and ghee', 'image' => 'gajar_halwa.jpg'],
                ['name' => 'Vanilla Ice Cream', 'type' => 'veg', 'mrp' => 50, 'desc' => 'Classic vanilla flavor scoop', 'image' => 'vanilla_icecream.jpg'],
                ['name' => 'Sizzling Brownie with Ice Cream', 'type' => 'veg', 'mrp' => 180, 'desc' => 'Hot chocolate brownie topped with vanilla ice cream', 'image' => 'sizzling_brownie.jpg'],
            ],
            'Emporium - Handicrafts & Souvenirs' => [
                ['name' => 'Marble Taj Mahal Replica (Small)', 'type' => 'goods', 'mrp' => 499, 'desc' => 'Handcrafted alabaster marble Taj Mahal miniature', 'image' => 'taj_mahal_small.jpg'],
                ['name' => 'Brass Peacock Idol', 'type' => 'goods', 'mrp' => 850, 'desc' => 'Pure solid brass decorative peacock artifact', 'image' => 'brass_peacock.jpg'],
                ['name' => 'Wooden Carved Jewellery Box', 'type' => 'goods', 'mrp' => 650, 'desc' => 'Traditional Sheesham wood carved storage box', 'image' => 'jewellery_box.jpg'],
                ['name' => 'Brass Ganesha Statue', 'type' => 'goods', 'mrp' => 1100, 'desc' => 'Handcrafted solid brass Lord Ganesha idol', 'image' => 'ganesha_statue.jpg'],
            ],
            'Emporium - Clothing & Apparel' => [
                ['name' => 'Men Cotton Kurta Pyjama Set', 'type' => 'goods', 'mrp' => 1299, 'desc' => '100% Pure cotton traditional ethnic wear', 'image' => 'kurta_pyjama.jpg'],
                ['name' => 'Women Bandhani Printed Saree', 'type' => 'goods', 'mrp' => 1850, 'desc' => 'Traditional Jaipuri Bandhej saree with blouse piece', 'image' => 'bandhani_saree.jpg'],
                ['name' => 'Men Silk Nehru Jacket', 'type' => 'goods', 'mrp' => 1499, 'desc' => 'Traditional sleeveless ethnic jacket', 'image' => 'nehru_jacket.jpg'],
            ],
            'Emporium - Leather Goods' => [
                ['name' => 'Genuine Leather Men Wallet', 'type' => 'goods', 'mrp' => 699, 'desc' => 'RFID blocking genuine leather bi-fold wallet', 'image' => 'leather_wallet.jpg'],
                ['name' => 'Handcrafted Leather Sling Bag', 'type' => 'goods', 'mrp' => 1499, 'desc' => 'Vintage style genuine goat leather messenger bag', 'image' => 'sling_bag.jpg'],
                ['name' => 'Leather Travel Duffle Bag', 'type' => 'goods', 'mrp' => 3499, 'desc' => 'Heavy duty weekender leather travel bag', 'image' => 'duffle_bag.jpg'],
            ],
            'Emporium - Jewelry & Accessories' => [
                ['name' => 'Kundan Necklace Set', 'type' => 'goods', 'mrp' => 2499, 'desc' => 'Traditional ethnic Kundan bridal jewelry set', 'image' => 'kundan_necklace.jpg'],
                ['name' => 'Silver Plated Oxidized Earrings', 'type' => 'goods', 'mrp' => 299, 'desc' => 'Boho style tribal oxidized drop earrings', 'image' => 'oxidized_earrings.jpg'],
            ],
            'Emporium - Spices & Teas' => [
                ['name' => 'Assam Orthodox Black Tea (250g)', 'type' => 'goods', 'mrp' => 350, 'desc' => 'Premium aromatic long-leaf tea pack', 'image' => 'assam_tea.jpg'],
                ['name' => 'Kashmiri Saffron / Kesar (1g)', 'type' => 'goods', 'mrp' => 490, 'desc' => 'Original organic grade-A Kashmiri saffron strands', 'image' => 'saffron.jpg'],
                ['name' => 'Indian Masala Chai Mix (200g)', 'type' => 'goods', 'mrp' => 250, 'desc' => 'Authentic blend of cardamom, ginger, and cloves', 'image' => 'masala_chai_mix.jpg'],
            ],
            'Emporium - Home Decor' => [
                ['name' => 'Handloom Cotton Cushion Covers (Set of 5)', 'type' => 'goods', 'mrp' => 699, 'desc' => 'Decorative printed cushion covers', 'image' => 'cushion_covers.jpg'],
                ['name' => 'Brass Hanging Bell', 'type' => 'goods', 'mrp' => 450, 'desc' => 'Traditional temple style brass bell for home', 'image' => 'brass_bell.jpg'],
            ],
        ];

        foreach ($categoriesWithItems as $categoryName => $items) {
            DB::table('item_categories')->updateOrInsert(
                ['name' => $categoryName],
                [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            $assignedTaxId = $categoryTaxMapping[$categoryName] ?? null;

            foreach ($items as $item) {
                DB::table('global_items')->updateOrInsert(
                    ['item_name' => $item['name'], 'category' => $categoryName],
                    [
                        'food_type' => $item['type'],
                        'mrp' => $item['mrp'],
                        'default_price' => $item['mrp'],
                        'tax_id' => $assignedTaxId,
                        'description' => $item['desc'],
                        'image' => $item['image'] ?? null,
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