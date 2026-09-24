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
    public function loadService($serviceName)
{
    // Agar member/hub/ folder ke andar ye view file exist karti hai toh load karegi
    if (view()->exists("member.hub.{$serviceName}")) {
        return view("member.hub.{$serviceName}");
    }
    return view('member.partials.coming-soon');
}
}