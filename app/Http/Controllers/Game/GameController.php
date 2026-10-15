<?php

namespace App\Http\Controllers\Game;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GameController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Agar database me tcoin balance ya main wallet balance alag hai:
        // Ex: $tcoinBalance = $user->tcoin ?? $user->wallet_balance ?? 0;
        $tcoinBalance = $user->tcoin_balance ?? $user->wallet_balance ?? 0.00;

        return view('game.index', compact('user', 'tcoinBalance'));
    }
}