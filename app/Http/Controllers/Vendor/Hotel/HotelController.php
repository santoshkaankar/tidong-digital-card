<?php

namespace App\Http\Controllers\Vendor\Hotel;

use App\Http\Controllers\Controller;
use App\Models\Vendor\Hotel\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HotelController extends Controller
{
    public function dashboard()
    {
        $vendor = Auth::user();
        $hotel = Hotel::where('user_id', $vendor->id)->first();

        return view('vendor.hotel.dashboard', compact('vendor', 'hotel'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'hotel_name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'contact_number' => 'nullable|string|max:20',
        ]);

        Hotel::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'hotel_name' => $request->hotel_name,
                'hotel_slug' => \Illuminate\Support\Str::slug($request->hotel_name),
                'category' => $request->category,
                'address' => $request->address,
                'contact_number' => $request->contact_number,
                'status' => 'active',
            ]
        );

        return redirect()->back()->with('success', 'Hotel details updated successfully.');
    }
}