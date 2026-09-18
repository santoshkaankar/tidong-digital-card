<?php

namespace App\Http\Controllers\Retail;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        // Cart items view load karne ke liye
        return view('retail.cart.index');
    }

    public function add(Request $request)
    {
        // Product cart me add karne ka logic
        return back()->with('success', 'Product cart me add ho gaya hai!');
    }
}