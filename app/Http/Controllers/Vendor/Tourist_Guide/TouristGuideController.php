<?php

namespace App\Http\Controllers\Vendor\Tourist_Guide;

use App\Http\Controllers\Controller;
use App\Models\Vendor\Tourist_Guide\TouristGuide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TouristGuideController extends Controller
{
    public function dashboard()
    {
        $vendor = Auth::user();
        $guide = TouristGuide::where('user_id', $vendor->id)->first();

        return view('vendor.tourist_guide.dashboard', compact('vendor', 'guide'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'agency_name' => 'required|string|max:255',
            'license_no' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:20',
            'languages' => 'nullable|string|max:255',
            'address' => 'nullable|string',
        ]);

        TouristGuide::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'agency_name' => $request->agency_name,
                'agency_slug' => \Illuminate\Support\Str::slug($request->agency_name),
                'license_no' => $request->license_no,
                'contact_number' => $request->contact_number,
                'languages' => $request->languages,
                'address' => $request->address,
                'status' => 'active',
            ]
        );

        return redirect()->back()->with('success', 'Tourist Guide details updated successfully.');
    }
}