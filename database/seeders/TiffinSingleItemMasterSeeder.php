<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Opt-in, idempotent reference prices for individual items only.
 * Not called by DatabaseSeeder and never overwrites existing master data.
 * No Thali, Combo, Tiffin meal or outlet-specific record is seeded.
 */
final class TiffinSingleItemMasterSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::hasTable('global_menu_items')
            || ! Schema::hasColumn('global_menu_items','mrp_paise')
            || ! Schema::hasColumn('global_menu_items','sale_price_paise')) {
            throw new \RuntimeException('Run the additive Global Item price migration first.');
        }
        $items = [
            // name, category, MRP rupees, suggested sale rupees
            ['Plain Roti','Indian Breads',12,10],
            ['Tawa Roti','Indian Breads',15,12],
            ['Butter Roti','Indian Breads',20,18],
            ['Plain Paratha','Indian Breads',40,35],
            ['Aloo Paratha','Indian Breads',65,55],
            ['Poori (1 Pc)','Indian Breads',15,12],
            ['Plain Rice','Rice',80,70],
            ['Jeera Rice','Rice',110,95],
            ['Steamed Basmati Rice','Rice',100,85],
            ['Dal Tadka','Dal',130,110],
            ['Yellow Dal','Dal',110,95],
            ['Dal Fry','Dal',125,105],
            ['Rajma Curry','Dal',140,120],
            ['Chole Curry','Dal',135,115],
            ['Aloo Jeera','Vegetable',100,85],
            ['Aloo Matar','Vegetable',125,105],
            ['Mixed Vegetable','Vegetable',145,125],
            ['Bhindi Masala','Vegetable',150,130],
            ['Cabbage Sabzi','Vegetable',105,90],
            ['Paneer Bhurji','Paneer',180,160],
            ['Shahi Paneer','Paneer',200,180],
            ['Kadai Paneer','Paneer',210,190],
            ['Plain Curd','Sides',50,40],
            ['Boondi Raita','Sides',75,65],
            ['Green Salad','Sides',60,50],
            ['Onion Salad','Sides',40,30],
            ['Papad Roasted','Sides',25,20],
            ['Mango Pickle','Sides',20,15],
            ['Green Chutney','Sides',20,15],
            ['Gulab Jamun (1 Pc)','Sweets',45,35],
            ['Kheer (1 Bowl)','Sweets',75,60],
            ['Poha','Breakfast',80,65],
            ['Upma','Breakfast',85,70],
            ['Plain Dosa','Breakfast',115,100],
            ['Idli (1 Pc)','Breakfast',25,20],
            ['Samosa (1 Pc)','Snacks',25,20],
            ['Bread Pakora (1 Pc)','Snacks',40,30],
            ['Masala Tea','Beverages',30,25],
            ['Plain Buttermilk','Beverages',40,35],
        ];
        $now=now();
        foreach($items as [$name,$category,$mrp,$sale]){
            if ($sale>$mrp || $sale<1) throw new \RuntimeException('Incorrect reference price.');
            $slug='single-'.Str::slug($name);
            // Existing names/slugs are never edited by this sample seeder.
            if(DB::table('global_menu_items')->where('slug',$slug)->exists()
               || DB::table('global_menu_items')->whereRaw('LOWER(name) = ?', [Str::lower($name)])->exists()) continue;
            DB::table('global_menu_items')->insertOrIgnore([
                'slug'=>$slug,'name'=>$name,'category'=>$category,
                'description'=>'Single item; example prices only. Outlet sets its own MRP and sale price.',
                'image_path'=>null,'mrp_paise'=>$mrp*100,'sale_price_paise'=>$sale*100,
                'is_active'=>true,'created_at'=>$now,'updated_at'=>$now,
            ]);
        }
    }
}
