<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Restaurant\TiffinCatalog;
use App\Models\Restaurant\TiffinCatalogItem;
use App\Models\Restaurant\RestaurantCategory;
use App\Models\Restaurant\RestaurantCustomItem;
use Illuminate\Support\Str;

class TiffinCatalogController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $tiffinCatalogs = TiffinCatalog::with('items')->latest()->get();
        
        $categories = RestaurantCategory::where('user_id', $userId)
            ->with(['items' => function ($query) use ($userId) {
                $query->where('user_id', $userId)->where('status', true)->with('globalItem');
            }])->get();

        $customItems = RestaurantCustomItem::where('user_id', $userId)
            ->where('is_available', true)->get();

        return view('vendor.restaurant.weekly-menu.index', compact('tiffinCatalogs', 'categories', 'customItems'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'meal_type' => 'required|string',
            'day' => 'required|string',
            'single_day_rate' => 'required|numeric',
            'full_week_rate' => 'required|numeric',
            'full_month_rate' => 'required|numeric',
            'items' => 'required|array|min:1',
        ]);

        $catalogTitle = ucfirst($request->meal_type) . ' - ' . ucfirst($request->day);

        $catalog = TiffinCatalog::create([
            'vendor_id' => auth()->id(),
            'title' => $catalogTitle,
            'delivery_address' => 'Standard Tiffin Service',
            'single_day_rate' => $request->single_day_rate,
            'full_week_rate' => $request->full_week_rate,
            'full_month_rate' => $request->full_month_rate,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        // Input items se item IDs extract karna (keys are item IDs)
        $itemsData = $request->input('items', []);
        $itemIds = array_keys($itemsData);

        TiffinCatalogItem::create([
            'tiffin_catalog_id' => $catalog->id,
            'day' => ucfirst($request->day),
            'meal_type' => $request->meal_type,
            'item_ids' => $itemIds,
        ]);

        return redirect()->back()->with('success', 'Tiffin Catalog successfully create ho gaya hai!');
    }

    

    public function show($id)
    {
        $userId = auth()->id();
        $catalogs = TiffinCatalog::with('items')->where('vendor_id', $userId)->get();

        foreach($catalogs as $catalog) {
            foreach($catalog->items as $catItem) {
                $names = [];
                if(is_array($catItem->item_ids)) {
                    // Custom items aur Restaurant items dono se names nikalne ka safe tareeqa
                    $customNames = \App\Models\Restaurant\RestaurantCustomItem::whereIn('id', $catItem->item_ids)->pluck('name')->toArray();
                    
                    // Agar item_ids mein standard/global items huye toh unke naam bhi fetch kar lenge
                    $regNames = \App\Models\Restaurant\RestaurantItem::whereIn('id', $catItem->item_ids)
                        ->with('globalItem')
                        ->get()
                        ->map(function($ri) {
                            return $ri->globalItem->item_name ?? $ri->globalItem->name ?? null;
                        })
                        ->filter()
                        ->toArray();

                    $names = array_merge($customNames, $regNames);
                }
                $catItem->resolved_item_names = $names;
            }
        }
        
        return view('vendor.restaurant.weekly-menu.show', compact('catalogs'));
    }

    public function edit($id)
    {
        $userId = auth()->id();
        $catalog = TiffinCatalog::with('items')->findOrFail($id);

        $categories = RestaurantCategory::where('user_id', $userId)
            ->with(['items' => function ($query) use ($userId) {
                $query->where('user_id', $userId)->where('status', true)->with('globalItem');
            }])->get();

        $customItems = RestaurantCustomItem::where('user_id', $userId)
            ->where('is_available', true)->get();

        return view('vendor.restaurant.weekly-menu.edit', compact('catalog', 'categories', 'customItems'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'meal_type' => 'required|string',
            'day' => 'required|string',
            'single_day_rate' => 'required|numeric',
            'full_week_rate' => 'required|numeric',
            'full_month_rate' => 'required|numeric',
            'items' => 'required|array|min:1',
        ]);

        $catalog = TiffinCatalog::findOrFail($id);
        
        $catalogTitle = ucfirst($request->meal_type) . ' - ' . ucfirst($request->day);

        $catalog->update([
            'title' => $catalogTitle,
            'single_day_rate' => $request->single_day_rate,
            'full_week_rate' => $request->full_week_rate,
            'full_month_rate' => $request->full_month_rate,
        ]);

        // Purane items delete karke naye update kar do
        $catalog->items()->delete();

        $itemsData = $request->input('items', []);
        $itemIds = array_keys($itemsData);

        TiffinCatalogItem::create([
            'tiffin_catalog_id' => $catalog->id,
            'day' => ucfirst($request->day),
            'meal_type' => $request->meal_type,
            'item_ids' => $itemIds,
        ]);

        return redirect()->route('vendor.restaurant.weekly-menu.index')->with('success', 'Tiffin Catalog successfully update ho gaya hai!');
    }

    public function destroy($id)
    {
        $catalog = TiffinCatalog::findOrFail($id);
        $catalog->items()->delete();
        $catalog->delete();

        return redirect()->back()->with('success', 'Tiffin Catalog delete kar diya gaya hai!');
    }
}