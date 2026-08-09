<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VendorCategory;
use App\Models\VendorItem;
use App\Models\GlobalItem;
use Illuminate\Support\Facades\Auth;

class BusinessCatalogController extends Controller
{
    // 1. Show page for selecting categories & picking items from Global Master
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

    // 2. Save Business Selected Categories
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

    // 3. Add Selected Global Items to Business Inventory
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
                    'description' => $gItem->description, // Default global description (store owner can edit later)
                    'mrp' => $gItem->mrp,
                    'sale_price' => $gItem->mrp, // Default sale price equals MRP
                    'status' => 'active'
                ]);
            }
        }

        return redirect()->back()->with('success', 'Selected items added to your shop inventory!');
    }

    // 4. Update Business Item Sale Price, Custom Description or Status
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
}