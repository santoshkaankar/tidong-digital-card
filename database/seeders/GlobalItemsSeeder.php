<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GlobalItemsSeeder extends Seeder
{
    public function run(): void
    {
        $categoriesWithItems = [
            // ==================== RESTAURANT & FOOD ITEMS ====================
            'Soups' => [
                ['name' => 'Tomato Soup', 'type' => 'veg', 'mrp' => 120, 'desc' => 'Classic creamy tomato soup served with croutons'],
                ['name' => 'Hot & Sour Soup', 'type' => 'veg', 'mrp' => 130, 'desc' => 'Spicy and tangy vegetable soup'],
                ['name' => 'Sweet Corn Soup', 'type' => 'veg', 'mrp' => 130, 'desc' => 'Mild sweet corn vegetable soup'],
                ['name' => 'Manchow Soup', 'type' => 'veg', 'mrp' => 140, 'desc' => 'Indo-Chinese soup served with crispy noodles'],
                ['name' => 'Lemon Coriander Soup', 'type' => 'veg', 'mrp' => 135, 'desc' => 'Healthy clear soup with fresh coriander and lemon'],
                ['name' => 'Clear Vegetable Soup', 'type' => 'veg', 'mrp' => 125, 'desc' => 'Light vegetable broth with chopped seasonal veggies'],
                ['name' => 'Mushroom Cream Soup', 'type' => 'veg', 'mrp' => 150, 'desc' => 'Rich creamy soup blended with fresh button mushrooms'],
                ['name' => 'Chicken Manchow Soup', 'type' => 'non-veg', 'mrp' => 160, 'desc' => 'Chicken soup topped with crispy noodles'],
                ['name' => 'Chicken Clear Soup', 'type' => 'non-veg', 'mrp' => 150, 'desc' => 'Light chicken broth with aromatic herbs'],
                ['name' => 'Chicken Hot & Sour Soup', 'type' => 'non-veg', 'mrp' => 165, 'desc' => 'Tangy and spicy chicken soup'],
                ['name' => 'Mutton Yakhni Soup', 'type' => 'non-veg', 'mrp' => 190, 'desc' => 'Aromatic spiced mutton bone broth'],
            ],
            'Starters & Snacks' => [
                ['name' => 'Paneer Tikka', 'type' => 'veg', 'mrp' => 240, 'desc' => 'Marinated paneer cubes grilled in tandoor'],
                ['name' => 'Paneer Malai Tikka', 'type' => 'veg', 'mrp' => 260, 'desc' => 'Creamy paneer cubes marinated with cashew and cream'],
                ['name' => 'Paneer Achari Tikka', 'type' => 'veg', 'mrp' => 250, 'desc' => 'Paneer cubes marinated in tangy Indian pickle spices'],
                ['name' => 'Veg Hara Bhara Kabab', 'type' => 'veg', 'mrp' => 200, 'desc' => 'Crispy spinach and green pea patties'],
                ['name' => 'Crispy Corn', 'type' => 'veg', 'mrp' => 180, 'desc' => 'Fried sweet corn tossed with spices'],
                ['name' => 'French Fries', 'type' => 'veg', 'mrp' => 120, 'desc' => 'Classic salted potato fries'],
                ['name' => 'Peri Peri Fries', 'type' => 'veg', 'mrp' => 140, 'desc' => 'Fries tossed in spicy peri peri seasoning'],
                ['name' => 'Spring Roll', 'type' => 'veg', 'mrp' => 170, 'desc' => 'Crispy rolls filled with shredded veggies'],
                ['name' => 'Cheese Corn Balls', 'type' => 'veg', 'mrp' => 220, 'desc' => 'Deep fried balls filled with molten cheese and corn'],
                ['name' => 'Dahi Ke Kebab', 'type' => 'veg', 'mrp' => 230, 'desc' => 'Crispy kebabs made with hung curd and spices'],
                ['name' => 'Mushroom Tikka', 'type' => 'veg', 'mrp' => 250, 'desc' => 'Spiced fresh mushrooms roasted in tandoor'],
                ['name' => 'Honey Chilli Potato', 'type' => 'veg', 'mrp' => 190, 'desc' => 'Crispy potatoes tossed in sweet chili sesame sauce'],
                ['name' => 'Soya Chaap Tikka', 'type' => 'veg', 'mrp' => 220, 'desc' => 'Soya chaap chunks marinated and roasted in tandoor'],
                ['name' => 'Malai Soya Chaap', 'type' => 'veg', 'mrp' => 240, 'desc' => 'Rich creamy and mild spiced soya chaap'],
                ['name' => 'Chicken Tikka', 'type' => 'non-veg', 'mrp' => 280, 'desc' => 'Boneless chicken marinated and cooked in tandoor'],
                ['name' => 'Tandoori Chicken (Half)', 'type' => 'non-veg', 'mrp' => 320, 'desc' => 'Traditional roasted chicken'],
                ['name' => 'Tandoori Chicken (Full)', 'type' => 'non-veg', 'mrp' => 580, 'desc' => 'Full roasted chicken in authentic tandoori spices'],
                ['name' => 'Chicken Seekh Kebab', 'type' => 'non-veg', 'mrp' => 290, 'desc' => 'Minced chicken skewers cooked over charcoal'],
                ['name' => 'Chicken Malai Tikka', 'type' => 'non-veg', 'mrp' => 310, 'desc' => 'Creamy boneless chicken kebabs'],
                ['name' => 'Chicken Wings', 'type' => 'non-veg', 'mrp' => 270, 'desc' => 'Crispy deep fried chicken wings tossed in sauce'],
                ['name' => 'Fish Tikka', 'type' => 'non-veg', 'mrp' => 360, 'desc' => 'Fish fillets marinated in tandoori spices and grilled'],
                ['name' => 'Amritsari Fish Fry', 'type' => 'non-veg', 'mrp' => 380, 'desc' => 'Crispy gram flour batter fried fish'],
                ['name' => 'Mutton Seekh Kebab', 'type' => 'non-veg', 'mrp' => 390, 'desc' => 'Minced mutton skewers grilled to perfection'],
            ],
            'Main Course - Veg' => [
                ['name' => 'Paneer Butter Masala', 'type' => 'veg', 'mrp' => 260, 'desc' => 'Paneer cooked in rich tomato and butter gravy'],
                ['name' => 'Kadhai Paneer', 'type' => 'veg', 'mrp' => 250, 'desc' => 'Paneer tossed with bell peppers and fresh spices'],
                ['name' => 'Shahi Paneer', 'type' => 'veg', 'mrp' => 270, 'desc' => 'Paneer in creamy cashew and onion gravy'],
                ['name' => 'Matar Paneer', 'type' => 'veg', 'mrp' => 230, 'desc' => 'Cottage cheese and green peas curry'],
                ['name' => 'Palak Paneer', 'type' => 'veg', 'mrp' => 250, 'desc' => 'Paneer cubes cooked in smooth spinach puree'],
                ['name' => 'Paneer Do Pyaza', 'type' => 'veg', 'mrp' => 260, 'desc' => 'Paneer curry with lots of caramelized onions'],
                ['name' => 'Paneer Lababdar', 'type' => 'veg', 'mrp' => 280, 'desc' => 'Rich onion tomato gravy with grated paneer'],
                ['name' => 'Dal Tadka', 'type' => 'veg', 'mrp' => 170, 'desc' => 'Yellow lentils tempered with garlic and cumin'],
                ['name' => 'Dal Makhani', 'type' => 'veg', 'mrp' => 220, 'desc' => 'Slow-cooked black lentils creamed with butter'],
                ['name' => 'Mix Vegetable', 'type' => 'veg', 'mrp' => 200, 'desc' => 'Assorted seasonal vegetables curry'],
                ['name' => 'Chana Masala', 'type' => 'veg', 'mrp' => 190, 'desc' => 'Spiced chickpea gravy cooked Punjabi style'],
                ['name' => 'Aloo Gobi', 'type' => 'veg', 'mrp' => 170, 'desc' => 'Potato and cauliflower dry curry'],
                ['name' => 'Malai Kofta', 'type' => 'veg', 'mrp' => 280, 'desc' => 'Soft cottage cheese balls in creamy rich gravy'],
                ['name' => 'Dum Aloo', 'type' => 'veg', 'mrp' => 210, 'desc' => 'Slow cooked baby potatoes in spicy gravy'],
                ['name' => 'Bhindi Masala', 'type' => 'veg', 'mrp' => 180, 'desc' => 'Okra cooked with onions and Indian spices'],
                ['name' => 'Mushroom Masala', 'type' => 'veg', 'mrp' => 250, 'desc' => 'Button mushrooms cooked in thick spicy gravy'],
                ['name' => 'Navratan Korma', 'type' => 'veg', 'mrp' => 290, 'desc' => 'Mild sweet curry with 9 veggies, fruits and nuts'],
                ['name' => 'Sev Tamatar', 'type' => 'veg', 'mrp' => 170, 'desc' => 'Rajasthani style spicy tomato curry topped with crispy sev'],
            ],
            'Main Course - Non-Veg' => [
                ['name' => 'Butter Chicken', 'type' => 'non-veg', 'mrp' => 340, 'desc' => 'Tender chicken in rich tomato butter gravy'],
                ['name' => 'Chicken Curry', 'type' => 'non-veg', 'mrp' => 300, 'desc' => 'Homestyle spiced chicken gravy'],
                ['name' => 'Kadhai Chicken', 'type' => 'non-veg', 'mrp' => 320, 'desc' => 'Chicken cooked with whole spices and capsicum'],
                ['name' => 'Chicken Korma', 'type' => 'non-veg', 'mrp' => 350, 'desc' => 'Mughlai style chicken in rich yogurt-cashew paste'],
                ['name' => 'Chicken Do Pyaza', 'type' => 'non-veg', 'mrp' => 330, 'desc' => 'Chicken curry prepared with diced onions'],
                ['name' => 'Handi Chicken', 'type' => 'non-veg', 'mrp' => 360, 'desc' => 'Chicken cooked slowly in traditional earthenware vessel'],
                ['name' => 'Mutton Curry', 'type' => 'non-veg', 'mrp' => 420, 'desc' => 'Slow cooked tender mutton curry'],
                ['name' => 'Mutton Rogan Josh', 'type' => 'non-veg', 'mrp' => 460, 'desc' => 'Kashmiri style mutton cooked in aromatic spices'],
                ['name' => 'Mutton Korma', 'type' => 'non-veg', 'mrp' => 450, 'desc' => 'Rich Mughlai mutton curry'],
                ['name' => 'Fish Curry', 'type' => 'non-veg', 'mrp' => 380, 'desc' => 'Fresh fish fillets cooked in spicy coastal gravy'],
            ],
            'Breads / Roti' => [
                ['name' => 'Tandoori Roti Plain', 'type' => 'veg', 'mrp' => 15, 'desc' => 'Whole wheat flatbread cooked in tandoor'],
                ['name' => 'Tandoori Butter Roti', 'type' => 'veg', 'mrp' => 20, 'desc' => 'Tandoori roti topped with butter'],
                ['name' => 'Plain Naan', 'type' => 'veg', 'mrp' => 40, 'desc' => 'Leavened fine flour bread'],
                ['name' => 'Butter Naan', 'type' => 'veg', 'mrp' => 50, 'desc' => 'Leavened bread topped with melted butter'],
                ['name' => 'Garlic Naan', 'type' => 'veg', 'mrp' => 65, 'desc' => 'Naan topped with chopped garlic and butter'],
                ['name' => 'Cheese Garlic Naan', 'type' => 'veg', 'mrp' => 90, 'desc' => 'Naan stuffed with cheese and garlic'],
                ['name' => 'Lachha Paratha', 'type' => 'veg', 'mrp' => 45, 'desc' => 'Layered crispy whole wheat bread'],
                ['name' => 'Stuffed Aloo Paratha', 'type' => 'veg', 'mrp' => 70, 'desc' => 'Paratha stuffed with spiced potato filling'],
                ['name' => 'Stuffed Paneer Paratha', 'type' => 'veg', 'mrp' => 90, 'desc' => 'Paratha stuffed with grated paneer and herbs'],
                ['name' => 'Missi Roti', 'type' => 'veg', 'mrp' => 30, 'desc' => 'Gram flour flatbread spiced with ajwain and herbs'],
                ['name' => 'Pudina Paratha', 'type' => 'veg', 'mrp' => 50, 'desc' => 'Layered paratha with dried mint flakes'],
            ],
            'Rice & Biryani' => [
                ['name' => 'Plain Steam Rice', 'type' => 'veg', 'mrp' => 110, 'desc' => 'Steamed long grain basmati rice'],
                ['name' => 'Jeera Rice', 'type' => 'veg', 'mrp' => 130, 'desc' => 'Basmati rice tempered with cumin seeds'],
                ['name' => 'Veg Fried Rice', 'type' => 'veg', 'mrp' => 160, 'desc' => 'Rice tossed with chopped veggies'],
                ['name' => 'Schezwan Fried Rice', 'type' => 'veg', 'mrp' => 180, 'desc' => 'Spicy fried rice with Schezwan sauce'],
                ['name' => 'Veg Pulao', 'type' => 'veg', 'mrp' => 180, 'desc' => 'Rice cooked with vegetables and mild spices'],
                ['name' => 'Matar Pulao', 'type' => 'veg', 'mrp' => 170, 'desc' => 'Basmati rice cooked with fresh green peas'],
                ['name' => 'Veg Dum Biryani', 'type' => 'veg', 'mrp' => 220, 'desc' => 'Aromatic rice layer cooked with marinated veggies'],
                ['name' => 'Paneer Biryani', 'type' => 'veg', 'mrp' => 250, 'desc' => 'Fragrant rice cooked with marinated paneer cubes'],
                ['name' => 'Chicken Biryani', 'type' => 'non-veg', 'mrp' => 280, 'desc' => 'Hyderabadi style layered chicken biryani'],
                ['name' => 'Chicken Boneless Biryani', 'type' => 'non-veg', 'mrp' => 320, 'desc' => 'Biryani cooked with juicy boneless chicken tikka'],
                ['name' => 'Mutton Biryani', 'type' => 'non-veg', 'mrp' => 380, 'desc' => 'Royal basmati rice cooked with tender mutton chunks'],
                ['name' => 'Egg Biryani', 'type' => 'non-veg', 'mrp' => 210, 'desc' => 'Spiced basmati rice layered with boiled eggs'],
            ],
            'South Indian' => [
                ['name' => 'Plain Dosa', 'type' => 'veg', 'mrp' => 90, 'desc' => 'Crispy rice crepe served with sambar and chutney'],
                ['name' => 'Masala Dosa', 'type' => 'veg', 'mrp' => 130, 'desc' => 'Crispy crepe filled with spiced potato masala'],
                ['name' => 'Butter Masala Dosa', 'type' => 'veg', 'mrp' => 150, 'desc' => 'Rich butter coated masala dosa'],
                ['name' => 'Paper Masala Dosa', 'type' => 'veg', 'mrp' => 160, 'desc' => 'Super thin crispy large masala dosa'],
                ['name' => 'Paneer Dosa', 'type' => 'veg', 'mrp' => 170, 'desc' => 'Dosa stuffed with spiced cottage cheese'],
                ['name' => 'Idli Sambar (2 Pcs)', 'type' => 'veg', 'mrp' => 80, 'desc' => 'Steamed rice cakes served with sambar'],
                ['name' => 'Vada Sambar (2 Pcs)', 'type' => 'veg', 'mrp' => 90, 'desc' => 'Crispy lentil donuts served with sambar'],
                ['name' => 'Onion Uttapam', 'type' => 'veg', 'mrp' => 130, 'desc' => 'Thick rice pancake topped with onions'],
                ['name' => 'Tomato Cheese Uttapam', 'type' => 'veg', 'mrp' => 150, 'desc' => 'Rice pancake topped with tomatoes and cheese'],
            ],
            'Fast Food & Pizza' => [
                ['name' => 'Veg Burger', 'type' => 'veg', 'mrp' => 80, 'desc' => 'Classic potato patty burger with mayo'],
                ['name' => 'Veg Cheese Burger', 'type' => 'veg', 'mrp' => 100, 'desc' => 'Veggie burger loaded with cheese slice'],
                ['name' => 'Paneer Burger', 'type' => 'veg', 'mrp' => 120, 'desc' => 'Burger with crispy paneer patty'],
                ['name' => 'Chicken Burger', 'type' => 'non-veg', 'mrp' => 130, 'desc' => 'Crispy chicken patty burger'],
                ['name' => 'Margherita Pizza', 'type' => 'veg', 'mrp' => 190, 'desc' => 'Classic cheese and tomato sauce pizza'],
                ['name' => 'Farmhouse Pizza', 'type' => 'veg', 'mrp' => 260, 'desc' => 'Topped with capsicum, onion, tomato, mushroom'],
                ['name' => 'Paneer Special Pizza', 'type' => 'veg', 'mrp' => 290, 'desc' => 'Loaded with spicy paneer cubes and veggies'],
                ['name' => 'Veg Grilled Sandwich', 'type' => 'veg', 'mrp' => 110, 'desc' => 'Toasted sandwich filled with veggies and cheese'],
                ['name' => 'Paneer Tikka Sandwich', 'type' => 'veg', 'mrp' => 140, 'desc' => 'Grilled sandwich filled with paneer tikka'],
            ],
            'Beverages' => [
                ['name' => 'Masala Chai', 'type' => 'veg', 'mrp' => 30, 'desc' => 'Indian spiced milk tea'],
                ['name' => 'Hot Coffee', 'type' => 'veg', 'mrp' => 50, 'desc' => 'Brewed milk coffee'],
                ['name' => 'Cold Coffee', 'type' => 'veg', 'mrp' => 110, 'desc' => 'Chilled coffee blended with ice cream'],
                ['name' => 'Sweet Lassi', 'type' => 'veg', 'mrp' => 70, 'desc' => 'Traditional Punjabi sweetened yogurt drink'],
                ['name' => 'Mango Lassi', 'type' => 'veg', 'mrp' => 90, 'desc' => 'Smoothie made with yogurt and mango pulp'],
                ['name' => 'Fresh Lime Soda', 'type' => 'veg', 'mrp' => 60, 'desc' => 'Refreshing lime drink with soda'],
                ['name' => 'Badam Milk', 'type' => 'veg', 'mrp' => 80, 'desc' => 'Chilled almond flavored milk'],
                ['name' => 'Mineral Water (1L)', 'type' => 'veg', 'mrp' => 20, 'desc' => 'Packaged drinking water'],
            ],
            'Desserts & Sweets' => [
                ['name' => 'Gulab Jamun (2 Pcs)', 'type' => 'veg', 'mrp' => 60, 'desc' => 'Classic sweet fried milk solids in sugar syrup'],
                ['name' => 'Rasgulla (2 Pcs)', 'type' => 'veg', 'mrp' => 60, 'desc' => 'Soft cottage cheese balls in sugar syrup'],
                ['name' => 'Rasmalai (2 Pcs)', 'type' => 'veg', 'mrp' => 90, 'desc' => 'Flattened paneer balls soaked in thickened milk'],
                ['name' => 'Gajar Ka Halwa', 'type' => 'veg', 'mrp' => 100, 'desc' => 'Traditional carrot pudding made with milk and ghee'],
                ['name' => 'Moong Dal Halwa', 'type' => 'veg', 'mrp' => 120, 'desc' => 'Rich lentils pudding cooked in pure desi ghee'],
                ['name' => 'Vanilla Ice Cream', 'type' => 'veg', 'mrp' => 50, 'desc' => 'Classic vanilla flavor scoop'],
                ['name' => 'Chocolate Ice Cream', 'type' => 'veg', 'mrp' => 70, 'desc' => 'Rich chocolate flavor scoop'],
                ['name' => 'Sizzling Brownie with Ice Cream', 'type' => 'veg', 'mrp' => 180, 'desc' => 'Hot chocolate brownie topped with vanilla ice cream'],
            ],

            // ==================== EMPORIUM & RETAIL ITEMS ====================
            'Emporium - Handicrafts & Souvenirs' => [
                ['name' => 'Marble Taj Mahal Replica (Small)', 'type' => 'goods', 'mrp' => 499, 'desc' => 'Handcrafted alabaster marble Taj Mahal miniature'],
                ['name' => 'Marble Taj Mahal Replica (Medium)', 'type' => 'goods', 'mrp' => 1299, 'desc' => 'Fine detail marble replica with inlay work'],
                ['name' => 'Brass Peacock Idol', 'type' => 'goods', 'mrp' => 850, 'desc' => 'Pure solid brass decorative peacock artifact'],
                ['name' => 'Wooden Carved Jewellery Box', 'type' => 'goods', 'mrp' => 650, 'desc' => 'Traditional Sheesham wood carved storage box'],
                ['name' => 'Blue Pottery Decorative Plate', 'type' => 'goods', 'mrp' => 750, 'desc' => 'Hand-painted traditional Rajasthani blue pottery'],
                ['name' => 'Brass Ganesha Statue', 'type' => 'goods', 'mrp' => 1100, 'desc' => 'Handcrafted solid brass Lord Ganesha idol'],
                ['name' => 'Wooden Elephant Pair', 'type' => 'goods', 'mrp' => 890, 'desc' => 'Carved Sheesham wood decorative elephants'],
                ['name' => 'Marble Inlay Coaster Set', 'type' => 'goods', 'mrp' => 650, 'desc' => 'Set of 6 white marble coasters with floral inlay'],
                ['name' => 'Handcrafted Camel Figurine', 'type' => 'goods', 'mrp' => 550, 'desc' => 'Decorated leather and brass camel showpiece'],
            ],
            'Emporium - Clothing & Apparel' => [
                ['name' => 'Men Cotton Kurta Pyjama Set', 'type' => 'goods', 'mrp' => 1299, 'desc' => '100% Pure cotton traditional ethnic wear'],
                ['name' => 'Women Bandhani Printed Saree', 'type' => 'goods', 'mrp' => 1850, 'desc' => 'Traditional Jaipuri Bandhej saree with blouse piece'],
                ['name' => 'Pashmina Touch Silk Stole', 'type' => 'goods', 'mrp' => 999, 'desc' => 'Soft printed shawl/stole for winter and travel'],
                ['name' => 'Hand Embroidered Leather Mojari', 'type' => 'goods', 'mrp' => 799, 'desc' => 'Ethnic Rajasthani footwear for men/women'],
                ['name' => 'Men Silk Nehru Jacket', 'type' => 'goods', 'mrp' => 1499, 'desc' => 'Traditional sleeveless ethnic jacket'],
                ['name' => 'Women Chikankari Kurti', 'type' => 'goods', 'mrp' => 1199, 'desc' => 'Handcrafted Lucknowi Chikankari cotton kurti'],
                ['name' => 'Silk Dupatta with Zari Work', 'type' => 'goods', 'mrp' => 850, 'desc' => 'Heavy traditional silk dupatta for festive wear'],
                ['name' => 'Rajasthani Printed Anarkali Suit', 'type' => 'goods', 'mrp' => 2200, 'desc' => 'Cotton flare ethnic suit with dupatta'],
            ],
            'Emporium - Leather Goods' => [
                ['name' => 'Genuine Leather Men Wallet', 'type' => 'goods', 'mrp' => 699, 'desc' => 'RFID blocking genuine leather bi-fold wallet'],
                ['name' => 'Handcrafted Leather Sling Bag', 'type' => 'goods', 'mrp' => 1499, 'desc' => 'Vintage style genuine goat leather messenger bag'],
                ['name' => 'Leather Travel Duffle Bag', 'type' => 'goods', 'mrp' => 3499, 'desc' => 'Heavy duty weekender leather travel bag'],
                ['name' => 'Women Leather Tote Bag', 'type' => 'goods', 'mrp' => 2199, 'desc' => 'Spacious genuine leather handbag'],
                ['name' => 'Leather Laptop Sleeve (15 Inch)', 'type' => 'goods', 'mrp' => 1299, 'desc' => 'Padded genuine leather laptop sleeve'],
                ['name' => 'Leather Belt for Men', 'type' => 'goods', 'mrp' => 599, 'desc' => 'Formal pure leather pin buckle belt'],
            ],
            'Emporium - Jewelry & Accessories' => [
                ['name' => 'Kundan Necklace Set', 'type' => 'goods', 'mrp' => 2499, 'desc' => 'Traditional ethnic Kundan bridal jewelry set'],
                ['name' => 'Silver Plated Oxidized Earrings', 'type' => 'goods', 'mrp' => 299, 'desc' => 'Boho style tribal oxidized drop earrings'],
                ['name' => 'Brass Metal Bangle Set', 'type' => 'goods', 'mrp' => 450, 'desc' => 'Set of 12 handcrafted designer metallic bangles'],
                ['name' => 'Meenakari Jhumka Earrings', 'type' => 'goods', 'mrp' => 599, 'desc' => 'Traditional enamelled Rajasthani jhumki'],
                ['name' => 'Pearl Choker Necklace', 'type' => 'goods', 'mrp' => 1299, 'desc' => 'Multi-strand faux pearl necklace set'],
            ],
            'Emporium - Spices & Teas' => [
                ['name' => 'Assam Orthodox Black Tea (250g)', 'type' => 'goods', 'mrp' => 350, 'desc' => 'Premium aromatic long-leaf tea pack'],
                ['name' => 'Kashmiri Saffron / Kesar (1g)', 'type' => 'goods', 'mrp' => 490, 'desc' => 'Original organic grade-A Kashmiri saffron strands'],
                ['name' => 'Indian Masala Chai Mix (200g)', 'type' => 'goods', 'mrp' => 250, 'desc' => 'Authentic blend of cardamom, ginger, and cloves'],
                ['name' => 'Darjeeling Green Tea (100g)', 'type' => 'goods', 'mrp' => 420, 'desc' => 'Pure unfermented whole leaf green tea'],
                ['name' => 'Cardamom Whole Green (100g)', 'type' => 'goods', 'mrp' => 380, 'desc' => 'Aromatic Elaichi pods directly from Kerala'],
                ['name' => 'Organic Turmeric Powder (250g)', 'type' => 'goods', 'mrp' => 150, 'desc' => 'Pure High-curcumin Haldi powder'],
            ],
            'Emporium - Home Decor' => [
                ['name' => 'Handloom Cotton Cushion Covers (Set of 5)', 'type' => 'goods', 'mrp' => 699, 'desc' => 'Decorative printed cushion covers'],
                ['name' => 'Brass Hanging Bell', 'type' => 'goods', 'mrp' => 450, 'desc' => 'Traditional temple style brass bell for home'],
                ['name' => 'Iron Craft Wall Hanging', 'type' => 'goods', 'mrp' => 1250, 'desc' => 'Modern metallic wall art sculpture'],
                ['name' => 'Handmade Jute Floor Mat', 'type' => 'goods', 'mrp' => 899, 'desc' => 'Eco-friendly braided jute area rug'],
                ['name' => 'Wooden Diya & Candle Holder', 'type' => 'goods', 'mrp' => 350, 'desc' => 'Carved Sheesham wood tea light candle holder'],
            ],
        ];

        foreach ($categoriesWithItems as $categoryName => $items) {
            // 1. Save Unique Categories
            DB::table('item_categories')->updateOrInsert(
                ['name' => $categoryName],
                [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            // 2. Save Unique Global Items
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