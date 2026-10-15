<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vendor\VendorBusiness;
use Illuminate\Support\Str;

class VendorBusinessController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $businesses = $user->vendorBusinesses;
        return view('vendor.businesses.index', compact('businesses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|max:255',
            'business_type' => 'required|string',
            'phone'         => 'nullable|string',
            'city'          => 'nullable|string',
            'address'       => 'nullable|string',
        ]);

        $business = VendorBusiness::create([
            'user_id'       => auth()->id(),
            'business_name' => $request->business_name,
            'business_type' => $request->business_type,
            'slug'          => Str::slug($request->business_name) . '-' . rand(1000, 9999),
            'phone'         => $request->phone,
            'city'          => $request->city,
            'address'       => $request->address,
            'is_active'     => true,
        ]);

        // Auto activate session for created business
        session([
            'active_business_id'   => $business->id,
            'active_business_name' => $business->business_name,
            'active_business_type' => $business->business_type,
        ]);

        return $this->redirectToBusinessModule($business->business_type);
    }

    public function switchBusiness($id)
    {
        $business = VendorBusiness::where('user_id', auth()->id())->findOrFail($id);

        session([
            'active_business_id'   => $business->id,
            'active_business_name' => $business->business_name,
            'active_business_type' => $business->business_type,
        ]);

        return $this->redirectToBusinessModule($business->business_type);
    }

    private function redirectToBusinessModule($type)
    {
        $type = strtolower($type);

        if ($type === 'restaurant') {
            return redirect()->route('vendor.restaurant.dashboard')->with('success', 'Entered Outlet Panel Successfully!');
        }

        // Future modules (Hotel, Taxi, Retail, etc.)
        if ($type === 'hotel' && \Route::has('vendor.hotel.dashboard')) {
            return redirect()->route('vendor.hotel.dashboard');
        }

        if ($type === 'taxi' && \Route::has('vendor.taxi.dashboard')) {
            return redirect()->route('vendor.taxi.dashboard');
        }

        return redirect()->route('vendor.dashboard')->with('success', 'Active business updated.');
    }
}