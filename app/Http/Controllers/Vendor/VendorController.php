<?php

namespace App\Http\Controllers\Vendor;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Vendor\VendorCategory;
use App\Models\Vendor\VendorItem;
use App\Models\Vendor\GlobalItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class VendorController extends Controller
{
    /**
     * Dashboard View
     */
    public function dashboard()
    {
        $userId = Auth::id();
        $totalItems = Vendoritem::where('user_id', $userId)->count();
        $totalCategories = VendorCategory::where('user_id', $userId)->count();

        return view('vendor.dashboard', compact('totalItems', 'totalCategories'));
    }

    /**
     * Display Categories Page
     */
    public function categoriesPage(Request $request)
    {
        $userId = Auth::id();

        if (Schema::hasColumn('vendor_categories', 'category_name')) {
            $selectedCategories = VendorCategory::where('user_id', $userId)->pluck('category_name')->toArray();
        } else {
            $selectedCategories = VendorCategory::where('user_id', $userId)->pluck('name')->toArray();
        }

        $allCategories = GlobalItem::where('is_approved', 1)->select('category')->distinct()->pluck('category');

        return view('vendor.categories', compact('allCategories', 'selectedCategories'));
    }

    /**
     * Save / Sync Categories
     */
    public function saveCategories(Request $request)
    {
        $request->validate([
            'categories' => 'required|array',
        ]);

        $userId = Auth::id();
        VendorCategory::where('user_id', $userId)->delete();

        foreach ($request->categories as $cat) {
            $data = ['user_id' => $userId];

            if (Schema::hasColumn('vendor_categories', 'category_name')) {
                $data['category_name'] = $cat;
            }
            if (Schema::hasColumn('vendor_categories', 'name')) {
                $data['name'] = $cat;
            }

            VendorCategory::create($data);
        }

        return redirect()->back()->with('success', 'Categories updated successfully!');
    }

    /**
     * Safe Category Deletion (Handles column check & ID/Name mismatch)
     */
    public function destroyCategory($id)
    {
        $userId = Auth::id();

        VendorCategory::where('user_id', $userId)
            ->where(function ($query) use ($id) {
                if (is_numeric($id)) {
                    $query->where('id', $id);
                } else {
                    if (Schema::hasColumn('vendor_categories', 'name')) {
                        $query->orWhere('name', $id);
                    }
                    if (Schema::hasColumn('vendor_categories', 'category_name')) {
                        $query->orWhere('category_name', $id);
                    }
                }
            })
            ->delete();

        return redirect()->back()->with('success', 'Category removed successfully!');
    }

    /**
     * Display Inventory Page
     */
    public function inventoryPage(Request $request)
    {
        $userId = Auth::id();
        $myInventory = Vendoritem::where('user_id', $userId)->get();
        // Only get Approved Global Items
        $globalItems = GlobalItem::where('is_approved', 1)->get();

        return view('vendor.inventory', compact('myInventory', 'globalItems'));
    }

    /**
     * Catalog Page Index
     */
    public function index(Request $request)
    {
        $userId = Auth::id();

        if (Schema::hasColumn('vendor_categories', 'category_name')) {
            $selectedCategories = VendorCategory::where('user_id', $userId)->pluck('category_name')->toArray();
        } else {
            $selectedCategories = VendorCategory::where('user_id', $userId)->pluck('name')->toArray();
        }

        $allCategories = GlobalItem::where('is_approved', 1)->select('category')->distinct()->pluck('category');

        // Only Approved items for global select
        $query = GlobalItem::where('is_approved', 1);
        
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

    /**
     * Add Global Items to Vendor Inventory
     */
    public function addItemsToInventory(Request $request)
    {
        $request->validate([
            'global_item_ids' => 'required|array',
        ]);

        $userId = Auth::id();

        foreach ($request->global_item_ids as $gId) {
            $gItem = GlobalItem::find($gId);
            if (!$gItem) continue;

            $exists = Vendoritem::where('user_id', $userId)
                ->where('item_name', $gItem->item_name)
                ->exists();

            if (!$exists) {
                $mrp = $gItem->mrp ?? $gItem->price ?? 0;
                $price = $gItem->price ?? $mrp;

                Vendoritem::create([
                    'user_id'     => $userId,
                    'category'    => $gItem->category ?? 'General',
                    'item_name'   => $gItem->item_name,
                    'description' => $gItem->description ?? '',
                    'mrp'         => $mrp,
                    'price'       => $price,
                    'status'      => 'active',
                    'is_available'=> 1
                ]);
            }
        }

        return redirect()->back()->with('success', 'Selected items added to inventory!');
    }

    /**
     * Update Single Item Details
     */
    public function updateInventoryItem(Request $request, $id)
    {
        $item = Vendoritem::where('user_id', Auth::id())->findOrFail($id);
        
        $price = $request->filled('sale_price') ? $request->sale_price : ($request->filled('price') ? $request->price : $item->price);

        $item->update([
            'mrp'         => $request->filled('mrp') ? $request->mrp : $item->mrp,
            'price'       => $price,
            'description' => $request->filled('description') ? $request->description : $item->description,
            'status'      => $request->filled('status') ? $request->status : $item->status
        ]);

        return redirect()->back()->with('success', 'Item updated successfully!');
    }

    /**
     * Remove Item from Inventory
     */
    public function destroyItem($id)
    {
        Vendoritem::where('user_id', Auth::id())->where('id', $id)->delete();
        return redirect()->back()->with('success', 'Item removed successfully!');
    }

    /**
     * Toggle Availability Status (AJAX)
     */
    public function toggleItemStatus(Request $request)
    {
        $request->validate([
            'item_id' => 'required',
            'status'  => 'required'
        ]);

        $item = Vendoritem::where('user_id', Auth::id())->findOrFail($request->item_id);
        $item->is_available = $request->status;
        $item->save();

        return response()->json([
            'success'      => true, 
            'message'      => 'Status updated successfully',
            'is_available' => $item->is_available
        ]);
    }

    /**
     * Display Pricing Page
     */
    public function pricingPage(Request $request)
    {
        $userId = Auth::id();
        $myInventory = Vendoritem::where('user_id', $userId)->get();

        if (view()->exists('vendor.pricing')) {
            return view('vendor.pricing', compact('myInventory'));
        }

        return view('vendor.inventory', compact('myInventory'));
    }

    /**
     * Update Bulk Pricing
     */
    public function updatePricing(Request $request)
    {
        $request->validate([
            'prices' => 'required|array',
        ]);

        foreach ($request->prices as $id => $priceData) {
            $item = Vendoritem::where('user_id', Auth::id())->find($id);
            if ($item) {
                $item->update([
                    'mrp'   => isset($priceData['mrp']) && $priceData['mrp'] !== '' ? $priceData['mrp'] : $item->mrp,
                    'price' => isset($priceData['sale_price']) && $priceData['sale_price'] !== '' ? $priceData['sale_price'] : $item->price,
                ]);
            }
        }

        return redirect()->back()->with('success', 'All prices updated successfully!');
    }

    /**
     * Submit Request for New Global Item (Pending Admin Approval)
     */
    public function requestNewItem(Request $request)
    {
        $request->validate([
            'item_name' => 'required|string|max:255',
            'category'  => 'nullable|string',
            'image'     => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('global-items', 'public');
        }

        // Create item in global_items with is_approved = 0 (Pending)
        GlobalItem::create([
            'requested_by' => Auth::id(),
            'item_name'    => $request->item_name,
            'category'     => $request->category ?? 'General',
            'image'        => $imagePath,
            'is_approved'  => 0, // Pending Status
        ]);

        return redirect()->back()->with('success', 'New item request submitted to admin for approval!');
    }

    /**
     * Generate & Show Vendor QR Code
     */
    public function showQrCode(Request $request)
    {
        $userId = Auth::id();
        $user = Auth::user();
        $slug = $user->slug ?? $userId;

        if (\Illuminate\Support\Facades\Route::has('public.menu')) {
            $menuUrl = route('public.menu', ['slug' => $slug]);
        } else {
            $menuUrl = url('/m/' . $slug);
        }

        if (view()->exists('vendor.qrcode')) {
            return view('vendor.qrcode', compact('menuUrl'));
        }

        return view('vendor.dashboard', compact('menuUrl', 'user'));
    }
}