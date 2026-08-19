<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class VendorManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('query');
        $vendor = null;

        if ($query) {
            $vendor = User::where('role', 'business')
                        ->where(function($q) use ($query) {
                            $q->where('email', $query)->orWhere('mobile', $query);
                        })->first();
        }

        return view('admin.vendors.manage', compact('vendor', 'query'));
    }

    public function saveOrUpdate(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'mobile' => 'required|string',
            'business_type' => 'nullable|string',
        ]);

        $vendor = User::updateOrCreate(
            ['email' => $request->email],
            [
                'name' => $request->name,
                'mobile' => $request->mobile,
                'role' => 'business',
                'business_type' => $request->business_type,
                'status' => $request->status ?? 'approved',
                'password' => $request->filled('password') ? Hash::make($request->password) : Hash::make(Str::random(8)),
            ]
        );

        return redirect()->route('admin.vendors.manage', ['query' => $vendor->email])
                         ->with('success', 'Vendor profile successfully saved/updated!');
    }
}