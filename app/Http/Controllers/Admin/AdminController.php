<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member\VisitingCard;
use App\Models\Vendor\GlobalItem;
use App\Models\Vendor\ItemCategory;
use App\Models\Vendor\VendorCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

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
            'totalUsers', 'totalCustomers', 'totalEmployees',
            'totalVendors', 'approvedVendors', 'unapprovedVendors', 
            'totalCards', 'pendingCards', 'totalGlobalItems', 
            'totalItemCategories', 'totalBusinesses'
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
        return redirect()->back()->with('success', 'Item approved successfully!');
    }

    public function rejectItem($id)
    {
        GlobalItem::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Item request rejected.');
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

    public function createVendorCategory()
    {
        $vendorCategories = VendorCategory::latest()->get();
        return view('admin.vendor-categories', compact('vendorCategories'));
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

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'string'],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => 'pending', 
            'business_type' => $request->business_type,
        ]);

        return redirect()->back()->with('success', 'New user registered successfully!');
    }

    // ==========================================
    // MANAGE CARDS & SEARCH / AUTO-REGISTER METHODS
    // ==========================================

    public function manageCardSearch(Request $request)
    {
        $query = $request->input('query'); // Email ya Mobile Number
        $card = null;
        $user = null;

        if ($query) {
            // User search karo email ya mobile se
            $user = User::where('email', $query)->orWhere('mobile', $query)->first();
            
            if ($user) {
                // Agar user hai toh uska visiting card dhoondo
                $card = VisitingCard::where('user_id', $user->id)->first();
            }
        }

        // Yeh member configuration/edit form view par data bhej dega
        return view('member.card.configure', compact('card', 'user', 'query'));
    }

    public function saveOrUpdateCard(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'mobile' => 'required',
            'name' => 'required|string',
        ]);

        $email = $request->email;
        $mobile = $request->mobile;
        $name = $request->name;

        // Check karo user pehle se database mein hai ya nahi
        $user = User::where('email', $email)->orWhere('mobile', $mobile)->first();

        if (!$user) {
            // Agar user nahi hai toh auto-register karo temporary password ke sath
            $tempPassword = Str::random(8);
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'mobile' => $mobile,
                'password' => Hash::make($tempPassword),
                'role' => 'member',
                'status' => 'approved',
            ]);
        }

        // Visiting card ko create ya update karo user_id ke base par
        VisitingCard::updateOrCreate(
            ['user_id' => $user->id],
            [
                'name' => $name,
                'email' => $email,
                'mobile' => $mobile,
                'business_name' => $request->business_name,
                'designation' => $request->designation,
                'tagline' => $request->tagline,
                // Aapke form ke baaki fields yahan map honge
            ]
        );

        return redirect()->route('admin.cards.manage')->with('success', 'Visiting Card & User Account successfully handled!');
    }
}