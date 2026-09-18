<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Retail\Category;
use App\Models\Retail\Product;

class RetailProductSeeder extends Seeder
{
    public function run(): void
    {
        $category = Category::create([
            'name' => 'Electronics',
            'slug' => 'electronics',
            'description' => 'Electronic items and gadgets'
        ]);

        Product::create([
            'vendor_id' => 1,
            'category_id' => $category->id,
            'title' => 'Smartphone Pro Max',
            'slug' => 'smartphone-pro-max',
            'description' => 'High performance smartphone with great camera.',
            'price' => 29999.00,
            'sale_price' => 24999.00,
            'stock' => 50,
            'sku' => 'ELEC-PHONE-01',
            'status' => 'published'
        ]);
    }
}