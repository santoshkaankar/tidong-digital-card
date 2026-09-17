<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\Restaurant\RestaurantStaffUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    // 1. Staff Page Render karne ke liye (Missing Method)
    public function index()
    {
        $vendorId = Auth::id() ?? session('active_restaurant_id');

        $staffMembers = RestaurantStaffUser::where('restaurant_id', $vendorId)
            ->latest()
            ->get();

        return view('vendor.restaurant.staff.index', compact('staffMembers'));
    }

    // 2. Naya Staff Save karne ke liye
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:restaurant_staff_users,email',
            'password' => 'required|string|min:6',
            'role'     => 'required|string',
        ]);

        $vendorId = Auth::id() ?? session('active_restaurant_id');

        RestaurantStaffUser::create([
            'restaurant_id' => $vendorId,
            'name'          => $request->name,
            'email'         => $request->email,
            'role'          => $request->role,
            'password'      => Hash::make($request->password),
            'status'        => 1,
        ]);

        return redirect()->back()->with('success', 'Staff member added successfully!');
    }

    // 3. Staff Delete karne ke liye
    public function destroy($id)
    {
        $vendorId = Auth::id() ?? session('active_restaurant_id');
        
        RestaurantStaffUser::where('restaurant_id', $vendorId)
            ->where('id', $id)
            ->delete();

        return redirect()->back()->with('success', 'Staff account deleted!');
    }
}