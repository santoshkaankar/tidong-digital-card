<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EmployeeManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('query');
        $employee = null;

        if ($query) {
            $employee = User::where('role', 'employee')
                        ->where(function($q) use ($query) {
                            $q->where('email', $query)->orWhere('mobile', $query);
                        })->first();
        }

        return view('admin.employees.manage', compact('employee', 'query'));
    }

    public function saveOrUpdate(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'mobile' => 'required|string',
        ]);

        $employee = User::updateOrCreate(
            ['email' => $request->email],
            [
                'name' => $request->name,
                'mobile' => $request->mobile,
                'role' => 'employee',
                'status' => $request->status ?? 'approved',
                'password' => $request->filled('password') ? Hash::make($request->password) : Hash::make(Str::random(8)),
            ]
        );

        return redirect()->route('admin.employees.manage', ['query' => $employee->email])
                         ->with('success', 'Employee profile successfully saved/updated!');
    }
}