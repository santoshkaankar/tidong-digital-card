<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Vendor\VendorEmployee;
use App\Models\Vendor\VendorBusiness;
use Illuminate\Support\Facades\Hash;

class VendorEmployeeController extends Controller
{
    public function index()
    {
        $vendorId = auth()->id();
        $businesses = VendorBusiness::where('user_id', $vendorId)->get();
        $employees = VendorEmployee::where('vendor_id', $vendorId)->with('business')->latest()->get();

        return view('vendor.employees.index', compact('businesses', 'employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'business_id' => 'required|exists:vendor_businesses,id',
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email|unique:vendor_employees,email',
            'phone'       => 'nullable|string',
            'password'    => 'required|string|min:6',
            'designation' => 'required|string',
            'permissions' => 'nullable|array',
        ]);

        // 1. Create main user record so Auth::attempt recognizes the employee login
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'mobile'   => $request->phone,
            'password' => Hash::make($request->password),
            'role'     => 'vendor_employee',
        ]);

        // 2. Create vendor employee mapping record
        VendorEmployee::create([
            'vendor_id'   => auth()->id(),
            'business_id' => $request->business_id,
            'user_id'     => $user->id,
            'name'        => $request->name,
            'email'       => $request->email,
            'phone'       => $request->phone,
            'password'    => Hash::make($request->password),
            'designation' => $request->designation,
            'permissions' => $request->permissions ?? [],
            'is_active'   => true,
        ]);

        return redirect()->back()->with('success', 'Employee registered successfully!');
    }

    public function toggleStatus($id)
    {
        $emp = VendorEmployee::where('vendor_id', auth()->id())->findOrFail($id);
        $emp->is_active = !$emp->is_active;
        $emp->save();

        return redirect()->back()->with('success', 'Employee status updated!');
    }
}