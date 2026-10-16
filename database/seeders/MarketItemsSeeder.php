<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Member\MarketItem;

class MarketItemsSeeder extends Seeder {
    public function run(): void {
        // Purane items clear karke saare purane + naye real-life items seed kar rahe hain
        MarketItem::truncate();

        $items = [
            // 📱 PHONES & GADGETS
            ['name' => 'Nokia 3310 Classic', 'category' => 'phones', 'price' => 50, 'item_type' => 'phone', 'color_hex' => '#111111'],
            ['name' => 'Samsung Galaxy S24 Ultra', 'category' => 'phones', 'price' => 1500, 'item_type' => 'phone', 'color_hex' => '#3b82f6'],
            ['name' => 'Apple iPhone 15 Pro Max', 'category' => 'phones', 'price' => 2000, 'item_type' => 'phone', 'color_hex' => '#6b7280'],
            ['name' => 'Apple iPhone 16 Pro Max', 'category' => 'phones', 'price' => 3500, 'item_type' => 'phone', 'color_hex' => '#d97706'],
            ['name' => 'Google Pixel 9 Pro', 'category' => 'phones', 'price' => 1800, 'item_type' => 'phone', 'color_hex' => '#4b5563'],
            ['name' => 'OnePlus 12 Pro', 'category' => 'phones', 'price' => 1200, 'item_type' => 'phone', 'color_hex' => '#dc2626'],

            // 🛠️ TOOLS & HOUSE MAINTENANCE (हथौड़ी, घास काटने की मशीन आदि)
            ['name' => 'Iron Hammer (हथौड़ी)', 'category' => 'tools', 'price' => 40, 'item_type' => 'tool', 'color_hex' => '#78350f'],
            ['name' => 'Screwdriver & Pliers Kit', 'category' => 'tools', 'price' => 70, 'item_type' => 'tool', 'color_hex' => '#4b5563'],
            ['name' => 'Lawn Mower (घास काटने की मशीन)', 'category' => 'tools', 'price' => 350, 'item_type' => 'machine', 'color_hex' => '#10b981'],
            ['name' => 'Heavy Toolbox (घर सुधार किट)', 'category' => 'tools', 'price' => 500, 'item_type' => 'tool', 'color_hex' => '#b91c1c'],
            ['name' => 'Paint Brush & Roller Set', 'category' => 'tools', 'price' => 60, 'item_type' => 'tool', 'color_hex' => '#eab308'],

            // 💡 HOME APPLIANCES & ELECTRONICS (लाइट, पंखा, टीवी, लैपटॉप, फ्रिज)
            ['name' => 'LED Bulb & Wiring Kit', 'category' => 'appliances', 'price' => 30, 'item_type' => 'light', 'color_hex' => '#facc15'],
            ['name' => 'Ceiling Fan (छत का पंखा)', 'category' => 'appliances', 'price' => 150, 'item_type' => 'fan', 'color_hex' => '#ffffff'],
            ['name' => 'Smart 55-inch LED TV', 'category' => 'appliances', 'price' => 900, 'item_type' => 'tv', 'color_hex' => '#000000'],
            ['name' => 'Gaming Laptop (i9 / RTX 4090)', 'category' => 'appliances', 'price' => 2200, 'item_type' => 'laptop', 'color_hex' => '#1e293b'],
            ['name' => 'Double Door Smart Fridge', 'category' => 'appliances', 'price' => 1400, 'item_type' => 'fridge', 'color_hex' => '#94a3b8'],
            ['name' => 'Gas Stove & Pipeline Set', 'category' => 'appliances', 'price' => 350, 'item_type' => 'kitchen', 'color_hex' => '#ea580c'],

            // 🪑 FURNITURE & INTERIOR DECOR (सोफा, कांच की खिड़की, फिश पॉट, बिस्तर)
            ['name' => 'Glass Window Panels (विंडो कांच)', 'category' => 'decor', 'price' => 200, 'item_type' => 'glass', 'color_hex' => '#38bdf8'],
            ['name' => 'Aquarium Fish Pot (मछली पॉट)', 'category' => 'decor', 'price' => 450, 'item_type' => 'decor', 'color_hex' => '#06b6d4'],
            ['name' => 'Wooden Luxury Sofa Set', 'category' => 'decor', 'price' => 1200, 'item_type' => 'sofa', 'color_hex' => '#854d0e'],
            ['name' => 'King Size Bedroom Mattress & Bed', 'category' => 'decor', 'price' => 1000, 'item_type' => 'bed', 'color_hex' => '#52525b'],
            ['name' => 'Ergonomic Gaming Chair', 'category' => 'decor', 'price' => 400, 'item_type' => 'chair', 'color_hex' => '#27272a'],
            ['name' => 'Dining Table & Kitchen Utensils', 'category' => 'decor', 'price' => 800, 'item_type' => 'kitchen', 'color_hex' => '#ca8a04'],

            // 🚿 BATHROOM & PERSONAL CARE (बाथटब, साबुन, शैंपू)
            ['name' => 'Luxury Bathroom Bathtub', 'category' => 'bathroom', 'price' => 1800, 'item_type' => 'bathtub', 'color_hex' => '#e2e8f0'],
            ['name' => 'Herbal Shampoo Bottle', 'category' => 'bathroom', 'price' => 35, 'item_type' => 'shampoo', 'color_hex' => '#16a34a'],
            ['name' => 'Premium Bath Soap Bar', 'category' => 'bathroom', 'price' => 20, 'item_type' => 'soap', 'color_hex' => '#f43f5e'],
            ['name' => 'Soft Bath Towel Set', 'category' => 'bathroom', 'price' => 90, 'item_type' => 'towel', 'color_hex' => '#0284c7'],

            // 🎒 EDUCATION & CAREER ITEMS (School/College vs Adult Job)
            ['name' => 'School Bag & Study Books (<19 Yrs)', 'category' => 'career', 'price' => 150, 'item_type' => 'school', 'color_hex' => '#2563eb'],
            ['name' => 'College Science Project Kit', 'category' => 'career', 'price' => 300, 'item_type' => 'college', 'color_hex' => '#9333ea'],
            ['name' => 'Corporate Office Briefcase (19+ Yrs)', 'category' => 'career', 'price' => 600, 'item_type' => 'job', 'color_hex' => '#1e1b4b'],
            ['name' => 'Executive Business Suit', 'category' => 'career', 'price' => 1500, 'item_type' => 'job', 'color_hex' => '#0f172a'],

            // 🏎️ CARS & MOTORS
            ['name' => 'Eco City Bicycle', 'category' => 'cars', 'price' => 15, 'item_type' => 'car', 'color_hex' => '#00ff00'],
            ['name' => 'Maruti WagonR', 'category' => 'cars', 'price' => 150, 'item_type' => 'car', 'color_hex' => '#ffffff'],
            ['name' => 'Mahindra Thar 4x4', 'category' => 'cars', 'price' => 600, 'item_type' => 'car', 'color_hex' => '#111111'],
            ['name' => 'Ford Mustang GT', 'category' => 'cars', 'price' => 1200, 'item_type' => 'car', 'color_hex' => '#10b981'],
            ['name' => 'BMW M5 CS', 'category' => 'cars', 'price' => 6000, 'item_type' => 'car', 'color_hex' => '#3b82f6'],
            ['name' => 'Lamborghini Aventador SVJ', 'category' => 'cars', 'price' => 18000, 'item_type' => 'car', 'color_hex' => '#eab308'],
            ['name' => 'Bugatti Chiron Hypercar', 'category' => 'cars', 'price' => 30000, 'item_type' => 'car', 'color_hex' => '#0284c7'],

            // 👔 CLOTHING & WEAR
            ['name' => 'Nike Air Jordan Sneakers', 'category' => 'clothing', 'price' => 500, 'item_type' => 'shoes', 'color_hex' => '#dc2626'],
            ['name' => 'Ray-Ban Aviators', 'category' => 'clothing', 'price' => 250, 'item_type' => 'glasses', 'color_hex' => '#000000'],
            ['name' => 'Gucci Designer Jacket', 'category' => 'clothing', 'price' => 3000, 'item_type' => 'clothes', 'color_hex' => '#15803d'],
            ['name' => 'Rolex Submariner Gold', 'category' => 'clothing', 'price' => 8000, 'item_type' => 'watch', 'color_hex' => '#eab308'],

            // 🍎 GROCERY & FOOD
            ['name' => 'Fresh Organic Apples', 'category' => 'grocery', 'price' => 20, 'item_type' => 'food', 'color_hex' => '#ff0000'],
            ['name' => 'Energy Drink Pack', 'category' => 'grocery', 'price' => 35, 'item_type' => 'food', 'color_hex' => '#3b82f6'],
            ['name' => 'Luxury Gourmet Basket', 'category' => 'grocery', 'price' => 150, 'item_type' => 'food', 'color_hex' => '#10b981'],
        ];

        foreach ($items as $item) {
            MarketItem::create($item);
        }
    }
}