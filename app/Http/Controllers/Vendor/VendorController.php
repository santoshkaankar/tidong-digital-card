<?php

namespace App\Http\Controllers\Vendor;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Vendor\VendorCategory;
use App\Models\Vendor\Vendoritem;
use App\Models\Vendor\GlobalItem;
use Illuminate\Support\Facades\Auth;

class VendorController extends Controller
{
    public function dashboard()
    {
        $userId = Auth::id();
        $totalItems = Vendoritem::where('user_id', $userId)->count();
        $totalCategories = VendorCategory::where('user_id', $userId)->count();

        return view('vendor.dashboard', compact('totalItems', 'totalCategories'));
    }

    public function categoriesPage(Request $request)
    {
        $userId = Auth::id();
        $selectedCategories = VendorCategory::where('user_id', $userId)->pluck('category_name')->toArray();
        if (empty($selectedCategories)) {
            $selectedCategories = VendorCategory::where('user_id', $userId)->pluck('name')->toArray();
        }
        $allCategories = GlobalItem::select('category')->distinct()->pluck('category');

        return view('vendor.categories', compact('allCategories', 'selectedCategories'));
    }

    public function inventoryPage(Request $request)
    {
        $userId = Auth::id();
        $myInventory = Vendoritem::where('user_id', $userId)->get();

        return view('vendor.inventory', compact('myInventory'));
    }

    public function pricingPage(Request $request)
    {
        return view('vendor.pricing');
    }

    public function updatePricing(Request $request, $id)
    {
        return redirect()->back()->with('success', 'Pricing updated successfully!');
    }

    public function index(Request $request)
    {
        $userId = Auth::id();

        $selectedCategories = VendorCategory::where('user_id', $userId)->pluck('category_name')->toArray();
        if (empty($selectedCategories)) {
            $selectedCategories = VendorCategory::where('user_id', $userId)->pluck('name')->toArray();
        }
        $allCategories = GlobalItem::select('category')->distinct()->pluck('category');

        $query = GlobalItem::query();
        
        if (!empty($selectedCategories)) {
            $query->whereIn('category', $selectedCategories);
        }

        if ($request->filled('search')) {
            $query->where('item_name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $globalItems = $query->get();
        $myInventory = Vendoritem::where('user_id', $userId)->get();

        return view('vendor.catalog', compact('allCategories', 'selectedCategories', 'globalItems', 'myInventory'));
    }

    public function saveCategories(Request $request)
    {
        $request->validate([
            'categories' => 'required|array',
        ]);

        $userId = Auth::id();
        VendorCategory::where('user_id', $userId)->delete();

        foreach ($request->categories as $cat) {
            VendorCategory::create([
                'user_id' => $userId,
                'category_name' => $cat,
                'name' => $cat
            ]);
        }

        return redirect()->back()->with('success', 'Categories updated successfully!');
    }

    public function addItemsToInventory(Request $request)
    {
        $request->validate([
            'global_item_ids' => 'required|array',
        ]);

        $userId = Auth::id();

        foreach ($request->global_item_ids as $gId) {
            $gItem = GlobalItem::findOrFail($gId);

            $exists = Vendoritem::where('user_id', $userId)
                ->where('item_name', $gItem->item_name)
                ->exists();

            if (!$exists) {
                $price = $gItem->mrp ?? $gItem->price ?? 0;
                Vendoritem::create([
                    'user_id' => $userId,
                    'category' => $gItem->category,
                    'item_name' => $gItem->item_name,
                    'description' => $gItem->description ?? '',
                    'mrp' => $price,
                    'sale_price' => $price,
                    'price' => $price,
                    'status' => 'active'
                ]);
            }
        }

        return redirect()->back()->with('success', 'Selected items added to inventory!');
    }

    public function updateInventoryItem(Request $request, $id)
    {
        $item = Vendoritem::where('user_id', Auth::id())->findOrFail($id);
        
        $price = $request->filled('sale_price') ? $request->sale_price : ($request->filled('price') ? $request->price : $item->mrp);

        $item->update([
            'sale_price' => $price,
            'price' => $price,
            'description' => $request->filled('description') ? $request->description : $item->description,
            'status' => $request->filled('status') ? $request->status : $item->status
        ]);

        return redirect()->back()->with('success', 'Item updated successfully!');
    }

    public function requestNewItem(Request $request)
    {
        return redirect()->back()->with('success', 'New item request submitted successfully!');
    }

    public function showQrCode(Request $request)
    {
        $userId = Auth::id();
        $menuUrl = route('menu.public', ['slug' => Auth::user()->slug ?? $userId]); 

        return view('vendor.qrcode', compact('menuUrl'));
    }
}