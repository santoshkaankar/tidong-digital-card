<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class MemberChildController extends Controller
{
    public function index()
    {
        $parentUser = Auth::user();
        
        // Fetch children accounts linked to this parent
        $children = DB::table('users')
                    ->where('parent_user_id', $parentUser->id)
                    ->get();

        return view('member.children.index', compact('parentUser', 'children'));
    }

    public function store(Request $request)
    {
        $parentUser = Auth::user();

        // Validation
        $request->validate([
            'name'      => 'required|string|max:255',
            'username'  => 'required|string|max:255|unique:users',
            'email'     => 'required|email|max:255|unique:users',
            'password'  => 'required|string|min:6',
            'dob'       => 'required|date',
            'position'  => 'required|in:left,right',
        ]);

        // Insert Child User (No 25k credit limit for child accounts, pocket money mini wallet logic)
        $childId = DB::table('users')->insertGetId([
            'parent_user_id'    => $parentUser->id,
            'is_child'          => true,
            'sponsor_id'        => $parentUser->id,
            'parent_id'         => $parentUser->id,
            'position'          => $request->position,
            'name'              => $request->name,
            'username'          => $request->username,
            'email'             => $request->email,
            'password'          => Hash::make($request->password),
            'dob'               => $request->dob,
            'role'              => 'member',
            'status'            => 'active',
            'credit_limit'      => 0.00, // Child gets no default 5L/25k credit limit
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        // Initialize Mini Wallet for Child (Pocket Money Wallet)
        DB::table('wallets')->insert([
            'user_id'                  => $childId,
            'real_balance'             => 0.00,
            'non_withdrawable_balance' => 0.00,
            't_coins'                  => 0.00,
            'credit_limit'             => 0.00,
            'credit_used'              => 0.00,
            'created_at'               => now(),
            'updated_at'               => now(),
        ]);

        return redirect()->back()->with('success', '🎉 Child sub-account created successfully with MLM position and mini pocket-money wallet!');
    }
}