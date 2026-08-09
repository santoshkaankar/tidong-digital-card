<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserAddress;

class UserAddressController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'house_no_mohalla' => 'required|string|max:255',
            'pincode'          => 'required|string|max:6',
            'area_name'        => 'nullable|string',
            'city'             => 'nullable|string',
            'district'         => 'nullable|string',
            'state'            => 'nullable|string',
        ]);

        UserAddress::create($validated);

        return redirect()->back()->with('success', 'Address successfully save ho gaya hai!');
    }
}