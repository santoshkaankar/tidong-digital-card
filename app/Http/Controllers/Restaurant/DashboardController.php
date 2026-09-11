<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    /**
     * Display the Restaurant Dashboard.
     */
    public function index()
    {
        return view('vendor.restaurant.dashboard');
    }
}