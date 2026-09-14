<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\Admin\GlobalItem;
use App\Models\Restaurant\RestaurantCategory;
use App\Models\Restaurant\RestaurantItem;
use App\Models\Restaurant\RestaurantCustomItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ItemController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        // 1. Vendor items (Global / Imported items)
        $items = RestaurantItem::where('user_id', $userId)
            ->with(['globalItem', 'category', 'tax'])
            ->latest()
            ->get();

        // 2. Vendor Custom Items (Personal Restaurant Items) - FIXED HERE
        $customItems = RestaurantCustomItem::where('user_id', $userId)
            ->with(['category', 'tax'])
            ->latest()
            ->get();

        // 3. Vendor Categories
        $categories = RestaurantCategory::where('user_id', $userId)->get();
        $categoryNames = $categories->pluck('name')->toArray();

        // 4. Filter Global items
        $selectedGlobalIds = $items->pluck('global_item_id')->filter()->toArray();

        $globalItems = GlobalItem::whereNotIn('id', $selectedGlobalIds)
            ->whereIn('category', $categoryNames)
            ->get();

        // 5. Active Taxes for Modals
        $taxes = DB::table('taxes')->where('is_active', 1)->get();

        return view('vendor.restaurant.items.index', compact('items', 'customItems', 'globalItems', 'categories', 'taxes'));
    }

    // Modal 1: Pick Existing Item from Global Catalog
    public function selectGlobalItem(Request $request)
    {
        $request->validate([
            'global_item_id' => 'required|exists:global_items,id',
            'tax_id'         => 'nullable|exists:taxes,id',
            'mrp'            => 'required|numeric|min:0',
            'price'          => 'nullable|numeric|min:0',
        ]);

        $mrp = $request->mrp;
        $price = ($request->filled('price') && $request->price > 0) ? $request->price : $mrp;

        $exists = RestaurantItem::where('user_id', auth()->id())
            ->where('global_item_id', $request->global_item_id)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Yeh item pehle se aapke menu me added hai!');
        }

        $globalItem = GlobalItem::find($request->global_item_id);
        $restaurantCategory = RestaurantCategory::where('user_id', auth()->id())
            ->where('name', $globalItem->category)
            ->first();

        RestaurantItem::create([
            'user_id'                => auth()->id(),
            'global_item_id'         => $request->global_item_id,
            'restaurant_category_id' => $restaurantCategory?->id,
            'tax_id'                 => $request->tax_id,
            'mrp'                    => $mrp,
            'price'                  => $price,
            'is_available'           => true,
            'status'                 => true,
        ]);

        return redirect()->back()->with('success', 'Global item successfully added to menu!');
    }

    // Modal 2: Save Custom Item to Global Master & Link to Menu (Alternatively handled by CustomItemController, but kept safe here)
    public function storeCustomItem(Request $request)
    {
        $request->validate([
            'name'                   => 'required|string|max:255',
            'restaurant_category_id' => 'nullable|exists:restaurant_categories,id',
            'category_name'          => 'nullable|string',
            'type'                   => 'required|in:veg,non-veg,egg',
            'tax_id'                 => 'nullable|exists:taxes,id',
            'mrp'                    => 'required|numeric|min:0',
            'price'                  => 'nullable|numeric|min:0',
        ]);

        $categoryName = 'General';
        if ($request->filled('restaurant_category_id')) {
            $category = RestaurantCategory::find($request->restaurant_category_id);
            if ($category) {
                $categoryName = $category->name;
            }
        } elseif ($request->filled('category_name')) {
            $categoryName = $request->category_name;
        }

        $mrp = $request->mrp;
        $price = ($request->filled('price') && $request->price > 0) ? $request->price : $mrp;

        DB::transaction(function () use ($request, $categoryName, $mrp, $price) {
            $globalItem = GlobalItem::create([
                'category'      => $categoryName,
                'item_name'     => $request->name,
                'food_type'     => $request->type,
                'mrp'           => $mrp,
                'default_price' => $price,
                'status'        => 'approved',
                'is_approved'   => true,
            ]);

            RestaurantItem::create([
                'user_id'                => auth()->id(),
                'global_item_id'         => $globalItem->id,
                'restaurant_category_id' => $request->restaurant_category_id,
                'tax_id'                 => $request->tax_id,
                'mrp'                    => $mrp,
                'price'                  => $price,
                'is_available'           => true,
                'status'                 => true,
            ]);
        });

        return redirect()->back()->with('success', 'Custom item successfully added to menu!');
    }

    public function destroy($id)
    {
        RestaurantItem::where('user_id', auth()->id())->findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Item removed from menu!');
    }
}