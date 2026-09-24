<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    public function index()
    {
        return view('delivery.dashboard');
    }

    public function dashboard()
    {
        return view('delivery.dashboard');
    }
}