<?php

namespace App\Http\Controllers\Vendor;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\VendorCategory;
use App\Models\VendorItem;
use App\Models\GlobalItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VendorController extends Controller
{
    // 1. Vendor Dashboard
    public function dashboard()
    {
        return view('vendor.dashboard');
    }

    // 2. Categories Page view
    public function categoriesPage(Request $request)
    {
        $userId = Auth::id();
        $selectedCategories = VendorCategory::where('user_id', $userId)->pluck('category_name')->toArray();
        $allCategories = GlobalItem::select('category')->distinct()->pluck('category');

        return view('vendor.categories', compact('allCategories', 'selectedCategories'));
    }

    // 3. Inventory / Items Page view
    public function inventoryPage(Request $request)
    {
        $userId = Auth::id();
        $myInventory = VendorItem::where('user_id', $userId)->get();

        return view('vendor.inventory', compact('myInventory'));
    }

    // 4. Pricing Page view
    public function pricingPage(Request $request)
    {
        return view('vendor.pricing');
    }

    // 5. Update Pricing
    public function updatePricing(Request $request, $id)
    {
        // Add pricing update logic here if needed
        return redirect()->back()->with('success', 'Pricing updated successfully!');
    }

    // 6. Catalog Index (Show page for selecting categories & picking items from Global Master)
    public function index(Request $request)
    {
        $userId = Auth::id();

        $selectedCategories = VendorCategory::where('user_id', $userId)->pluck('category_name')->toArray();
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

    // 7. Save Business Selected Categories
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
                'category_name' => $cat
            ]);
        }

        return redirect()->back()->with('success', 'Categories updated successfully! Now you can pick items.');
    }

    // 8. Add Selected Global Items to Business Inventory
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
                VendorItem::create([
                    'user_id' => $userId,
                    'category' => $gItem->category,
                    'item_name' => $gItem->item_name,
                    'description' => $gItem->description,
                    'mrp' => $gItem->mrp,
                    'sale_price' => $gItem->mrp,
                    'status' => 'active'
                ]);
            }
        }

        return redirect()->back()->with('success', 'Selected items added to your shop inventory!');
    }

    // 9. Update Business Item Sale Price, Custom Description or Status
    public function updateInventoryItem(Request $request, $id)
    {
        $item = VendorItem::where('user_id', Auth::id())->findOrFail($id);
        
        $item->update([
            'sale_price' => $request->filled('sale_price') ? $request->sale_price : $item->mrp,
            'description' => $request->filled('description') ? $request->description : $item->description,
            'status' => $request->filled('status') ? $request->status : $item->status
        ]);

        return redirect()->back()->with('success', 'Item updated successfully for your panel!');
    }

    // 10. Request New Item
    public function requestNewItem(Request $request)
    {
        return redirect()->back()->with('success', 'New item request submitted successfully!');
    }

    // 11. Show QR Code
    public function showQrCode(Request $request)
    {
        return view('vendor.qrcode');
    }
}