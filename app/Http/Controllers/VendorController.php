<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VendorItem;
use App\Models\VendorCategory;
use App\Models\GlobalItem;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class VendorController extends Controller
{
    public function dashboard()
    {
        $userId = Auth::id();
        $totalItems = VendorItem::where('user_id', $userId)->count();
        $totalCategories = VendorCategory::where('user_id', $userId)->count();

        return view('vendor.dashboard', compact('totalItems', 'totalCategories'));
    }

    // Separate Categories Page View
    public function categoriesPage()
    {
        $userId = Auth::id();
        $selectedCategories = VendorCategory::where('user_id', $userId)->pluck('name')->toArray();
        $allCategories = GlobalItem::select('category')->distinct()->pluck('category');

        return view('vendor.categories', compact('allCategories', 'selectedCategories'));
    }

    // Separate Inventory Page View
    public function inventoryPage(Request $request)
    {
        $userId = Auth::id();
        $selectedCategories = VendorCategory::where('user_id', $userId)->pluck('name')->toArray();

        $query = GlobalItem::query();
        if (!empty($selectedCategories)) {
            $query->whereIn('category', $selectedCategories);
        }

        if ($request->filled('search')) {
            $query->where('item_name', 'like', '%' . $request->search . '%');
        }

        $globalItems = $query->get();
        $myInventory = VendorItem::where('user_id', $userId)->get();

        return view('vendor.inventory', compact('globalItems', 'myInventory', 'selectedCategories'));
    }

    public function index(Request $request)
    {
        $userId = Auth::id();

        $selectedCategories = VendorCategory::where('user_id', $userId)->pluck('name')->toArray();
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
        $myInventory = VendorItem::where('user_id', $userId)->get();

        return view('vendor.catalog', compact('allCategories', 'selectedCategories', 'globalItems', 'myInventory'));
    }

    public function saveCategories(Request $request)
    {
        $request->validate([
            'categories' => 'required|array',
        ]);

        $userId = Auth::id();
        
        foreach ($request->categories as $cat) {
            $exists = VendorCategory::where('user_id', $userId)->where('name', $cat)->exists();
            if (!$exists) {
                VendorCategory::create([
                    'user_id' => $userId,
                    'name' => $cat
                ]);
            }
        }

        return redirect()->route('vendor.categories.index')->with('success', 'Categories updated successfully!');
    }

    public function addItemsToInventory(Request $request)
    {
        $request->validate([
            'global_item_ids' => 'required|array',
        ]);

        $userId = Auth::id();

        foreach ($request->global_item_ids as $gId) {
            $gItem = GlobalItem::findOrFail($gId);

            $exists = VendorItem::where('user_id', $userId)
                ->where('item_name', $gItem->item_name)
                ->exists();

            if (!$exists) {
                // Price ke liye safe fallback lagaya hai taaki database error na aaye
                $itemPrice = $gItem->mrp ?? $gItem->default_price ?? $gItem->price ?? 0;

                VendorItem::create([
                    'user_id' => $userId,
                    'category' => $gItem->category,
                    'item_name' => $gItem->item_name,
                    'description' => $gItem->description ?? '',
                    'price' => $itemPrice, 
                    'status' => 'active'
                ]);
            }
        }

        return redirect()->route('vendor.inventory.index')->with('success', 'Selected items added to your shop inventory!');
    }
    
    public function showQrCode()
    {
        $userId = Auth::id();
        $menuUrl = route('menu.public', ['slug' => $userId]); 
        $qrcode = QrCode::size(250)->generate($menuUrl);

        return view('vendor.qrcode', compact('qrcode', 'menuUrl'));
    }
}