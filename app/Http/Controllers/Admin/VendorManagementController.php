<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vendor\GlobalItem;
use App\Models\Vendor\ItemCategory;
use App\Models\Vendor\VendorCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VendorManagementController extends Controller
{
    // ==========================================
    // 1. VENDOR PROFILES MANAGEMENT
    // ==========================================

    public function index(Request $request)
    {
        $query = $request->input('query');
        $vendor = null;

        if ($query) {
            $vendor = User::where('role', 'business')
                        ->where(function($q) use ($query) {
                            $q->where('email', $query)->orWhere('mobile', $query);
                        })->first();
        }

        return view('admin.vendors.manage', compact('vendor', 'query'));
    }

    public function saveOrUpdate(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'mobile' => 'required|string',
            'business_type' => 'nullable|string',
        ]);

        $vendor = User::updateOrCreate(
            ['email' => $request->email],
            [
                'name' => $request->name,
                'mobile' => $request->mobile,
                'role' => 'business',
                'business_type' => $request->business_type,
                'status' => $request->status ?? 'approved',
                'password' => $request->filled('password') ? Hash::make($request->password) : Hash::make(Str::random(8)),
            ]
        );

        return redirect()->route('admin.vendors.manage', ['query' => $vendor->email])
                         ->with('success', 'Vendor profile successfully saved/updated!');
    }

    // ==========================================
    // 2. VENDOR CATEGORIES MANAGEMENT
    // ==========================================

    public function createVendorCategory()
    {
        $vendorCategories = VendorCategory::latest()->get();
        return view('admin.vendors.vendor-categories', compact('vendorCategories'));
    }

    public function storeVendorCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:vendor_categories,name',
        ]);

        VendorCategory::create([
            'name' => trim($request->name),
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', 'Vendor Category added successfully!');
    }

    public function destroyVendorCategory($id)
    {
        VendorCategory::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Vendor Category deleted successfully!');
    }

    // ==========================================
    // 3. GLOBAL ITEMS & ITEM CATEGORIES MANAGEMENT
    // ==========================================

    public function indexGlobalItem()
    {
        $items = GlobalItem::latest()->get();
        return view('admin.vendors.global-items-index', compact('items'));
    }

    public function createGlobalItem()
    {
        $itemCategories = ItemCategory::all();
        return view('admin.vendors.create-global-item', compact('itemCategories'));
    }

    public function storeItemCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:item_categories,name',
        ]);

        ItemCategory::create(['name' => trim($request->name)]);
        return redirect()->back()->with('success', 'Item Category added successfully!');
    }

    public function storeGlobalItem(Request $request)
    {
        $request->validate([
            'category' => 'required|string|max:255',
            'item_name' => 'required|string|max:255',
            'item_pic' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'mrp' => 'required|numeric|min:0',
        ]);

        $imagePath = null;
        if ($request->hasFile('item_pic')) {
            $imagePath = $request->file('item_pic')->store('global-items', 'public');
        }

        GlobalItem::create([
            'category' => trim($request->category),
            'item_name' => trim($request->item_name),
            'item_pic' => $imagePath,
            'mrp' => $request->mrp,
            'default_price' => $request->mrp,
            'description' => $request->description,
            'status' => 'approved', 
        ]);

        return redirect()->back()->with('success', 'Global item created successfully!');
    }

    /**
     * Edit Global Item Form Display
     */
    public function editGlobalItem($id)
    {
        $item = GlobalItem::findOrFail($id);
        $itemCategories = ItemCategory::all();
        return view('admin.vendors.edit-global-item', compact('item', 'itemCategories'));
    }

    /**
     * Update Global Item Data
     */
    public function updateGlobalItem(Request $request, $id)
    {
        $item = GlobalItem::findOrFail($id);

        $request->validate([
            'category' => 'required|string|max:255',
            'item_name' => 'required|string|max:255',
            'item_pic' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'mrp' => 'required|numeric|min:0',
        ]);

        if ($request->hasFile('item_pic')) {
            if ($item->item_pic && Storage::disk('public')->exists($item->item_pic)) {
                Storage::disk('public')->delete($item->item_pic);
            }
            $item->item_pic = $request->file('item_pic')->store('global-items', 'public');
        }

        $item->update([
            'category' => trim($request->category),
            'item_name' => trim($request->item_name),
            'mrp' => $request->mrp,
            'default_price' => $request->mrp,
            'description' => $request->description,
            'item_pic' => $item->item_pic,
        ]);

        return redirect()->route('admin.global.items.index')->with('success', 'Global item updated successfully!');
    }

    public function destroyGlobalItem($id)
    {
        $item = GlobalItem::findOrFail($id);
        if ($item->item_pic && Storage::disk('public')->exists($item->item_pic)) {
            Storage::disk('public')->delete($item->item_pic);
        }
        $item->delete();

        return redirect()->back()->with('success', 'Global Item deleted successfully!');
    }

    // ==========================================
    // 4. ITEM APPROVALS & MODERATION
    // ==========================================

    public function pendingItems()
    {
        $pendingItems = GlobalItem::where('status', 'pending')->get();
        return view('admin.vendors.pending-items', compact('pendingItems'));
    }

    public function approveItem($id)
    {
        $item = GlobalItem::findOrFail($id);
        $item->update(['status' => 'approved']);
        return redirect()->back()->with('success', 'Item approved successfully!');
    }

    public function rejectItem($id)
    {
        $item = GlobalItem::findOrFail($id);
        if ($item->item_pic && Storage::disk('public')->exists($item->item_pic)) {
            Storage::disk('public')->delete($item->item_pic);
        }
        $item->delete();
        return redirect()->back()->with('success', 'Item request rejected.');
    }
}