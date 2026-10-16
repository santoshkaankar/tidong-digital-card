<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class MemberChildController extends Controller
{
    // List all child sub-accounts
    public function index()
    {
        $parent = Auth::user();
        $children = User::where('parent_user_id', $parent->id)->get();
        return view('member.children.index', compact('children'));
    }

    // Create a new Child Account under Member
    public function store(Request $request)
    {
        $parent = Auth::user();

        $request->validate([
            'name'        => 'required|string|max:255',
            'username'    => 'required|string|unique:users,username',
            'password'    => 'required|min:6',
            'permissions' => 'nullable|array'
        ]);

        $child = User::create([
            'name'           => $request->name,
            'username'       => $request->username,
            'email'          => $parent->id . '_child_' . rand(100,999) . '@tidong.in', // Auto-generated internal email
            'mobile'         => $parent->mobile, // Linked to Parent's Mobile
            'password'       => Hash::make($request->password),
            'role'           => 'member',
            'parent_user_id' => $parent->id,
            'is_child'       => true,
            'permissions'    => json_encode($request->permissions ?? ['play_game']),
            'tcoins'         => 0, // Children use Main Parent Wallet TCoins
        ]);

        return redirect()->back()->with('success', 'Child sub-account created successfully!');
    }
}
