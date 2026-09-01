<?php

namespace App\Http\Controllers\Vendor;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\VendorItem;
use App\Models\GlobalItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VendorCatalogController extends Controller
{
    // 1. Dashboard
    public function dashboard()
    {
        return view('vendor.dashboard');
    }

    // 2. Categories Page View
    public function categoriesPage(Request $request)
    {
        $user = Auth::user();

        // 1. Logged in vendor ki DB me existing categories fetch karna
        $selectedCategories = [];

        if (isset($user->categories)) {
            $selectedCategories = is_array($user->categories) ? $user->categories : json_decode($user->categories, true) ?? [];
        } else {
            // Check in item_categories or vendor_categories table if exists
            $selectedCategories = DB::table('item_categories')->pluck('name')->toArray();
        }

        // 2. All available Master Categories for Dropdown
        $allCategories = GlobalItem::select('category')->distinct()->pluck('category')->toArray();

        if (empty($allCategories)) {
            $allCategories = ['Fast-food & Snakes', 'Brack-fast', 'Lunch-food', 'Dinner-food'];
        }

        return view('vendor.categories', compact('allCategories', 'selectedCategories'));
    }

    // 3. Save / Append New Category (Strict Fix For Overwriting Issue)
    public function saveCategories(Request $request)
    {
        $user = Auth::user();
        
        // Form field ki input value nikalna (Single String or Array)
        $newCategory = $request->input('categories') ?? $request->input('category');

        if (empty($newCategory)) {
            return redirect()->back()->with('error', 'Please select a category.');
        }

        // Convert input into array format
        $incomingArr = is_array($newCategory) ? $newCategory : [$newCategory];

        // --- STEP 1: Fetch Existing Saved Categories ---
        $existingCategories = [];
        if (isset($user->categories)) {
            $existingCategories = is_array($user->categories) ? $user->categories : (json_decode($user->categories, true) ?? []);
        } else {
            $existingCategories = DB::table('item_categories')->pluck('name')->toArray();
        }

        // --- STEP 2: Merge Existing + New Categories (Remove Duplicates) ---
        $mergedCategories = array_unique(array_merge($existingCategories, $incomingArr));

        // --- STEP 3: Save Merged Data Back to DB ---
        // Case A: If user model has 'categories' field
        if (Schema::hasColumn('users', 'categories')) {
            $user->categories = json_encode(array_values($mergedCategories));
            $user->save();
        }

        // Case B: Insert in 'item_categories' table without deleting existing
        foreach ($incomingArr as $cat) {
            $catName = trim($cat);
            if (!empty($catName)) {
                $exists = DB::table('item_categories')->where('name', $catName)->exists();
                if (!$exists) {
                    DB::table('item_categories')->insert([
                        'name'       => $catName,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
        }

        return redirect()->back()->with('success', 'Categories updated successfully!');
    }

    // 4. Destroy / Remove Single Category
    public function destroyCategory(Request $request, $id)
    {
        $user = Auth::user();

        if (isset($user->categories) && Schema::hasColumn('users', 'categories')) {
            $currentArr = is_array($user->categories) ? $user->categories : (json_decode($user->categories, true) ?? []);
            
            // Remove $id or category string from array
            $updatedArr = array_filter($currentArr, function($value) use ($id) {
                return $value !== $id;
            });

            $user->categories = json_encode(array_values($updatedArr));
            $user->save();
        }

        // Also delete from table if present
        if (is_numeric($id)) {
            DB::table('item_categories')->where('id', $id)->delete();
        } else {
            DB::table('item_categories')->where('name', $id)->delete();
        }

        return redirect()->back()->with('success', 'Category removed successfully!');
    }

    // 5. Inventory Page View
    public function inventoryPage(Request $request)
    {
        $userId = Auth::id();
        $myInventory = VendorItem::where('user_id', $userId)->get();

        return view('vendor.inventory', compact('myInventory'));
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

    // 8. Catalog Index
    public function index(Request $request)
    {
        $userId = Auth::id();
        $user = Auth::user();

        $selectedCategories = [];
        if (isset($user->categories)) {
            $selectedCategories = is_array($user->categories) ? $user->categories : json_decode($user->categories, true) ?? [];
        } else {
            $selectedCategories = DB::table('item_categories')->pluck('name')->toArray();
        }

        $allCategories = GlobalItem::select('category')->distinct()->pluck('category')->toArray();

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

    // 9. Add Items to Inventory
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
                    'user_id'     => $userId,
                    'category'    => $gItem->category,
                    'item_name'   => $gItem->item_name,
                    'description' => $gItem->description,
                    'mrp'         => $gItem->mrp,
                    'sale_price'  => $gItem->mrp,
                    'status'      => 'active'
                ]);
            }
        }

        return redirect()->back()->with('success', 'Selected items added to inventory!');
    }

    // 10. Update Inventory Item
    public function updateInventoryItem(Request $request, $id)
    {
        $item = VendorItem::where('user_id', Auth::id())->findOrFail($id);
        
        $item->update([
            'sale_price'  => $request->filled('sale_price') ? $request->sale_price : $item->mrp,
            'description' => $request->filled('description') ? $request->description : $item->description,
            'status'      => $request->filled('status') ? $request->status : $item->status
        ]);

        return redirect()->back()->with('success', 'Item updated successfully!');
    }

    // 11. Request New Item
    public function requestNewItem(Request $request)
    {
        return redirect()->back()->with('success', 'New item request submitted!');
    }

    // 12. Show QR Code
    public function showQrCode(Request $request)
    {
        return view('vendor.qrcode');
    }
}