<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HubController extends Controller
{
    public function index(Request $request)
    {
        $currentLang = $request->get('lang', 'en');
        return view('member.hub', compact('currentLang'));
    }
}