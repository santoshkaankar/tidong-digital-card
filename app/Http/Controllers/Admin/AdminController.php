<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member\VisitingCard;
use App\Models\Vendor\GlobalItem;
use App\Models\Vendor\ItemCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    /**
     * Display Admin Main Dashboard with stats.
     */
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

    /**
     * Delete Visiting Card
     */
    public function destroy($id)
    {
        VisitingCard::findOrFail($id)->delete();
        return back()->with('success', 'Card deleted successfully!');
    }

    /**
     * Direct User Creation by Admin
     */
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
    // MEMBER / VISITING CARD MANAGEMENT
    // ==========================================

    public function manageCardSearch(Request $request)
    {
        $query = $request->input('query');
        $card = null;
        $user = null;

        if ($query) {
            $user = User::where('email', $query)->orWhere('mobile', $query)->first();
            if ($user) {
                $card = VisitingCard::where('user_id', $user->id)->first();
            }
        }

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

        $user = User::where('email', $email)->orWhere('mobile', $mobile)->first();

        if (!$user) {
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

        VisitingCard::updateOrCreate(
            ['user_id' => $user->id],
            [
                'name' => $name,
                'email' => $email,
                'mobile' => $mobile,
                'business_name' => $request->business_name,
                'designation' => $request->designation,
                'tagline' => $request->tagline,
            ]
        );

        return redirect()->route('admin.cards.manage')->with('success', 'Visiting Card & User Account successfully handled!');
    }
}