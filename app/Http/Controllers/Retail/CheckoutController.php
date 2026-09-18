<?php

namespace App\Http\Controllers\Retail;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function index()
    {
        return view('retail.checkout.index');
    }

    public function process(Request $request)
    {
        // Order place aur payment process karne ka logic
        return redirect()->route('retail.shop.index')->with('success', 'Order safalta-purvak place ho gaya hai!');
    }
}