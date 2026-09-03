<?php

namespace App\Http\Controllers\Vendor;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Vendor\VendorItem;
use App\Models\Vendor\ItemCategory;
use App\Models\Vendor\GlobalItem;
use App\Models\Vendor\VendorCategory;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class VendorCatalogController extends Controller
{
    // 1. Dashboard View
    public function dashboard()
    {
        return view('vendor.dashboard');
    }

    // 2. Categories Page View
    public function categoriesPage(Request $request)
    {
        $userId = Auth::id();

        // Master Categories list for dropdown
        $allCategories = ItemCategory::pluck('name')->toArray();

        if (empty($allCategories)) {
            $allCategories = GlobalItem::whereNotNull('category')->distinct()->pluck('category')->toArray();
        }

        // Vendor Selected Categories
        $selectedCategories = VendorCategory::where('user_id', $userId)->pluck('name')->toArray();

        return view('vendor.categories', compact('allCategories', 'selectedCategories'));
    }

    // 3. Save Selected Category
    public function saveCategories(Request $request)
    {
        $categoryName = trim($request->input('category') ?? $request->input('categories'));

        if (empty($categoryName)) {
            return redirect()->back()->with('error', 'Please select a category.');
        }

        $userId = Auth::id();

        ItemCategory::firstOrCreate(['name' => $categoryName]);

        VendorCategory::firstOrCreate([
            'user_id' => $userId,
            'name'    => $categoryName
        ]);

        return redirect()->route('vendor.categories.index')->with('success', 'Category added successfully!');
    }

    // 4. Delete / Remove Category for Vendor
    public function destroyCategory(Request $request, $id)
    {
        $categoryName = urldecode($id);
        $userId = Auth::id();

        VendorCategory::where('user_id', $userId)->where('name', $categoryName)->delete();

        return redirect()->route('vendor.categories.index')->with('success', 'Category removed successfully!');
    }

    // 5. Inventory Page View (Loads items matching only selected categories)
    public function inventoryPage(Request $request)
    {
        $userId = Auth::id();

        // 1. Get categories selected by this vendor
        $selectedCategories = VendorCategory::where('user_id', $userId)->pluck('name')->toArray();

        // 2. Get Global Items belonging ONLY to selected categories ($globalItems variable for blade file)
        $globalItems = GlobalItem::whereIn('category', $selectedCategories)->get();

        // 3. Get Vendor's current Inventory items
        $myInventory = VendorItem::where('user_id', $userId)->get();

        return view('vendor.inventory', compact('globalItems', 'myInventory', 'selectedCategories'));
    }

    // 6. Pricing Page View
    public function pricingPage(Request $request)
    {
        return view('vendor.pricing');
    }

    // 7. Update Pricing
    public function updatePricing(Request $request, $id)
    {
        return redirect()->back()->with('success', 'Pricing updated successfully!');
    }

    // 8. Catalog Index Page
    public function index(Request $request)
    {
        $userId = Auth::id();

        $allCategories = ItemCategory::pluck('name')->toArray();
        $selectedCategories = VendorCategory::where('user_id', $userId)->pluck('name')->toArray();

        $query = Schema::hasTable('global_items') ? DB::table('global_items') : null;
        $globalItems = collect([]);

        if ($query) {
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
        }

        $myInventory = VendorItem::where('user_id', $userId)->get();

        return view('vendor.catalog', compact('allCategories', 'selectedCategories', 'globalItems', 'myInventory'));
    }

    // 9. Add Selected Items to Vendor Inventory
    public function addItemsToInventory(Request $request)
    {
        $userId = Auth::id();

        // Extract item ID from array or single input
        $globalItemIds = $request->input('global_item_ids', []);
        $singleId = $request->input('global_item_id') ?? $request->input('item_id');

        if (!empty($singleId)) {
            $globalItemIds[] = $singleId;
        }

        if (empty($globalItemIds)) {
            return redirect()->back()->with('error', 'Please select an item to add.');
        }

        foreach ($globalItemIds as $gId) {
            if (empty($gId)) continue;

            $gItem = GlobalItem::find($gId);

            if ($gItem) {
                $exists = VendorItem::where('user_id', $userId)
                    ->where('item_name', $gItem->item_name)
                    ->exists();

                if (!$exists) {
                    VendorItem::create([
                        'user_id'     => $userId,
                        'category'    => $gItem->category,
                        'item_name'   => $gItem->item_name,
                        'description' => $gItem->description ?? '',
                        'mrp'         => $gItem->mrp ?? 0,
                        'price'       => $gItem->default_price ?? $gItem->mrp ?? 0,
                        'status'      => 'active'
                    ]);
                }
            }
        }

        return redirect()->back()->with('success', 'Selected item added to inventory!');
    }

    // 10. Update Single Inventory Item
    public function updateInventoryItem(Request $request, $id)
    {
        $item = VendorItem::where('user_id', Auth::id())->findOrFail($id);

        $item->update([
            'price'       => $request->filled('price') ? $request->price : $item->price,
            'description' => $request->filled('description') ? $request->description : $item->description,
            'status'      => $request->filled('status') ? $request->status : $item->status
        ]);

        return redirect()->back()->with('success', 'Item updated successfully!');
    }

    // 11. Toggle Item Status (Active / Inactive)
    public function toggleItemStatus(Request $request)
    {
        if ($request->has('id')) {
            $item = VendorItem::where('user_id', Auth::id())->find($request->id);
            if ($item) {
                $item->status = $item->status === 'active' ? 'inactive' : 'active';
                $item->save();
                return redirect()->back()->with('success', 'Item status updated!');
            }
        }
        return redirect()->back()->with('error', 'Item not found.');
    }

    // 12. Request New Item
    public function requestNewItem(Request $request)
    {
        return redirect()->back()->with('success', 'New item request submitted successfully!');
    }

    // 13. Show QR Code Page
    public function showQrCode(Request $request)
    {
        return view('vendor.qrcode');
    }
}