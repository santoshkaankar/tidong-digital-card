<?php

namespace App\Http\Controllers\Vendor\Emporium;

use App\Http\Controllers\Controller;
use App\Models\Vendor\Emporium\Emporium;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmporiumController extends Controller
{
    public function dashboard()
    {
        $vendor = Auth::user();
        $emporium = Emporium::where('user_id', $vendor->id)->first();

        return view('vendor.emporium.dashboard', compact('vendor', 'emporium'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'store_name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'contact_number' => 'nullable|string|max:20',
        ]);

        Emporium::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'store_name' => $request->store_name,
                'store_slug' => \Illuminate\Support\Str::slug($request->store_name),
                'category' => $request->category,
                'address' => $request->address,
                'contact_number' => $request->contact_number,
                'status' => 'active',
            ]
        );

        return redirect()->back()->with('success', 'Emporium details updated successfully.');
    }
}