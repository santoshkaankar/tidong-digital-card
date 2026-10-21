<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GlobalMenuCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        // Insert missing examples only. Never replace vendor data or edit existing catalog entries.
        DB::table('global_menu_items')->insertOrIgnore([
            ['slug'=>'masala-dosa','name'=>'Masala Dosa','category'=>'South Indian','description'=>'Crispy dosa with chutney and sambar','image_path'=>'/images/global-menu/masala-dosa.webp','is_active'=>true,'created_at'=>$now,'updated_at'=>$now],
            ['slug'=>'idli-sambar','name'=>'Idli Sambar','category'=>'South Indian','description'=>'Steamed idli with sambar','image_path'=>'/images/global-menu/idli-sambar.webp','is_active'=>true,'created_at'=>$now,'updated_at'=>$now],
            ['slug'=>'medu-vada','name'=>'Medu Vada','category'=>'South Indian','description'=>'Crisp lentil fritters','image_path'=>'/images/global-menu/medu-vada.webp','is_active'=>true,'created_at'=>$now,'updated_at'=>$now],
            ['slug'=>'paneer-butter-masala','name'=>'Paneer Butter Masala','category'=>'North Indian','description'=>'Creamy paneer gravy','image_path'=>'/images/global-menu/paneer-butter-masala.webp','is_active'=>true,'created_at'=>$now,'updated_at'=>$now],
            ['slug'=>'dal-tadka','name'=>'Dal Tadka','category'=>'North Indian','description'=>'Spiced yellow lentils','image_path'=>'/images/global-menu/dal-tadka.webp','is_active'=>true,'created_at'=>$now,'updated_at'=>$now],
            ['slug'=>'butter-naan','name'=>'Butter Naan','category'=>'North Indian','description'=>'Soft butter naan','image_path'=>'/images/global-menu/butter-naan.webp','is_active'=>true,'created_at'=>$now,'updated_at'=>$now],
            ['slug'=>'veg-burger','name'=>'Veg Burger','category'=>'Fast Food','description'=>'Vegetarian burger','image_path'=>'/images/global-menu/veg-burger.webp','is_active'=>true,'created_at'=>$now,'updated_at'=>$now],
            ['slug'=>'french-fries','name'=>'French Fries','category'=>'Fast Food','description'=>'Crisp potato fries','image_path'=>'/images/global-menu/french-fries.webp','is_active'=>true,'created_at'=>$now,'updated_at'=>$now],
            ['slug'=>'veg-biryani','name'=>'Veg Biryani','category'=>'Rice','description'=>'Vegetable biryani rice','image_path'=>'/images/global-menu/veg-biryani.webp','is_active'=>true,'created_at'=>$now,'updated_at'=>$now],
            ['slug'=>'jeera-rice','name'=>'Jeera Rice','category'=>'Rice','description'=>'Cumin-flavored rice','image_path'=>'/images/global-menu/jeera-rice.webp','is_active'=>true,'created_at'=>$now,'updated_at'=>$now],
            ['slug'=>'veg-manchurian','name'=>'Veg Manchurian','category'=>'Chinese','description'=>'Vegetarian Manchurian','image_path'=>'/images/global-menu/veg-manchurian.webp','is_active'=>true,'created_at'=>$now,'updated_at'=>$now],
            ['slug'=>'hakka-noodles','name'=>'Hakka Noodles','category'=>'Chinese','description'=>'Stir-fried noodles','image_path'=>'/images/global-menu/hakka-noodles.webp','is_active'=>true,'created_at'=>$now,'updated_at'=>$now],
            ['slug'=>'masala-chai','name'=>'Masala Chai','category'=>'Beverages','description'=>'Indian spiced tea','image_path'=>'/images/global-menu/masala-chai.webp','is_active'=>true,'created_at'=>$now,'updated_at'=>$now],
            ['slug'=>'filter-coffee','name'=>'Filter Coffee','category'=>'Beverages','description'=>'South Indian filter coffee','image_path'=>'/images/global-menu/filter-coffee.webp','is_active'=>true,'created_at'=>$now,'updated_at'=>$now],
            ['slug'=>'gulab-jamun','name'=>'Gulab Jamun','category'=>'Desserts','description'=>'Soft milk-solid sweet','image_path'=>'/images/global-menu/gulab-jamun.webp','is_active'=>true,'created_at'=>$now,'updated_at'=>$now],
            ['slug'=>'samosa','name'=>'Samosa','category'=>'Snacks','description'=>'Crispy vegetable-filled pastry','image_path'=>'/images/global-menu/samosa.webp','is_active'=>true,'created_at'=>$now,'updated_at'=>$now],
        ]);
    }
}
