<?php

namespace App\Http\Controllers;

use App\Models\VisitingCard;
use App\Models\GlobalItem;
use App\Models\ItemCategory;
use App\Models\VendorCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class AdminController extends Controller
{
    public function index()
    {
        $totalUsers = User::count(); 
        $totalCustomers = User::where('role', 'user')->count(); 
        $totalEmployees = User::where('role', 'employee')->count(); 
        $totalVendors = User::where('role', 'business')->count();
        
        $approvedVendors = User::where('role', 'business')->where('status', 'approved')->count(); 
        $unapprovedVendors = User::where('role', 'business')->where('status', '!=', 'approved')->count();

        $totalCards = VisitingCard::count();
        
        $pendingCards = 0;
        if (Schema::hasColumn('visiting_cards', 'status')) {
            $pendingCards = VisitingCard::where('status', 'pending')->count();
        }

        $totalGlobalItems = GlobalItem::count();
        $totalItemCategories = ItemCategory::count();
        $totalBusinesses = $totalVendors;

        return view('admin.dashboard', compact(
            'totalUsers', 
            'totalCustomers',
            'totalEmployees',
            'totalVendors', 
            'approvedVendors', 
            'unapprovedVendors', 
            'totalCards', 
            'pendingCards', 
            'totalGlobalItems', 
            'totalItemCategories', 
            'totalBusinesses'
        ));
    }

    public function destroy($id)
    {
        VisitingCard::findOrFail($id)->delete();
        return back()->with('success', 'Card deleted successfully!');
    }

    public function pendingItems()
    {
        $pendingItems = GlobalItem::where('status', 'pending')->get();
        return view('admin.pending-items', compact('pendingItems'));
    }

    public function approveItem($id)
    {
        $item = GlobalItem::findOrFail($id);
        $item->update(['status' => 'approved']);

        return redirect()->back()->with('success', 'Item approved and added to Global Master successfully!');
    }

    public function rejectItem($id)
    {
        GlobalItem::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Item request rejected and removed.');
    }

    public function createGlobalItem()
    {
        $itemCategories = ItemCategory::all();
        return view('admin.create-global-item', compact('itemCategories'));
    }

    public function storeItemCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:item_categories,name',
        ]);

        ItemCategory::create([
            'name' => trim($request->name)
        ]);

        return redirect()->back()->with('success', 'Item Category added successfully!');
    }

    public function storeGlobalItem(Request $request)
    {
        $request->validate([
            'category' => 'required|string|max:255',
            'item_name' => 'required|string|max:255',
            'item_pic' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'mrp' => 'required|numeric|min:0',
            'description' => 'nullable|string',
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

        return redirect()->back()->with('success', 'Global item created and auto-approved successfully!');
    }

    // --- Vendor Categories Management Methods ---
    public function createVendorCategory()
    {
        $vendorCategories = VendorCategory::latest()->get();
        return view('admin.vendor-categories', compact('vendorCategories'));
    }

    public function storeVendorCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:vendor_categories,name',
            'description' => 'nullable|string',
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

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'string'],
            'business_type' => ['nullable', 'string'],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => 'pending', 
            'business_type' => $request->business_type,
        ]);

        return redirect()->back()->with('success', 'New user/business registered successfully!');
    }
}