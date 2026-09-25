<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    // 1. Show Restaurant Settings Page
    public function index()
    {
        $restaurant = Auth::user();
        return view('vendor.restaurant.setting.settings', compact('restaurant'));
    }

    // 2. Save / Update Settings
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'                => 'required|string|max:255',
            'mobile'              => 'required|string|max:20',
            'food_type'           => 'required|in:pure_veg,non_veg,both',
            'pincode'             => 'required|string|max:10',
            'area'                => 'nullable|string|max:255',
            'city'                => 'required|string|max:255',
            'state'               => 'required|string|max:255',
            'address'             => 'required|string|max:500',
            'gstin'               => 'nullable|string|max:20',
            'account_holder_name' => 'nullable|string|max:255',
            'bank_name'           => 'nullable|string|max:255',
            'account_number'      => 'nullable|string|max:50',
            'ifsc_code'           => 'nullable|string|max:20',
            'upi_id'              => 'nullable|string|max:100',
            'profile_photo'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
        ]);

        $data = $request->only([
            'name', 'mobile', 'food_type', 'pincode', 
            'area', 'city', 'state', 'address', 'gstin',
            'account_holder_name', 'bank_name', 'account_number', 'ifsc_code', 'upi_id'
        ]);

        // Restaurant Photo Upload
        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            $imagePath = $request->file('profile_photo')->store('restaurants', 'public');
            $data['profile_photo'] = $imagePath;
        }

        $user->update($data);

        return redirect()->back()->with('success', 'Restaurant settings saved successfully!');
    }

    // 3. Location Dynamic Search API (Pincode, Area, City)
    public function searchLocation(Request $request)
    {
        $search = $request->get('term') ?? $request->get('q');

        if (empty($search)) {
            return response()->json([]);
        }

        // Exact DB column matching: office_name, district, state_name, pincode
        $locations = DB::table('pincodes')
            ->where('pincode', 'LIKE', "%{$search}%")
            ->orWhere('office_name', 'LIKE', "%{$search}%")
            ->orWhere('district', 'LIKE', "%{$search}%")
            ->orWhere('state_name', 'LIKE', "%{$search}%")
            ->limit(15)
            ->get(['pincode', 'office_name', 'district', 'state_name']);

        return response()->json($locations);
    }
}