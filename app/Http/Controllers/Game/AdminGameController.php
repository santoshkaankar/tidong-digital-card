<?php

namespace App\Http\Controllers\Game;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AdminGameController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Admin (Papa) aur Employee (Gaurang/Saharsh) dono game manage kar sakte hain
        $players = User::where('role', 'member')->orWhere('role', 'customer')->get();

        return view('game.admin', compact('players', 'user'));
    }

    public function updateEconomy(Request $request)
    {
        // Admin/Employee control action
        return redirect()->back()->with('success', 'Game economy updated!');
    }
}