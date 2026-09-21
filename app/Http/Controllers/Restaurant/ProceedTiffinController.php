<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Restaurant\TiffinCatalog;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class ProceedTiffinController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();
        
        $duration = $request->input('duration', '1d'); // 1d, 1w, 1m, custom
        $selectedDate = $request->input('date', date('Y-m-d'));
        $fromDate = $request->input('from_date', date('Y-m-d'));
        $toDate = $request->input('to_date', date('Y-m-d', strtotime('+7 days')));
        $mealTypes = $request->input('meal_types', ['breakfast', 'lunch', 'dinner']); // array

        $dayNames = [];

        if ($duration == '1d' || $duration == 'custom') {
            $dayNames[] = Carbon::parse($selectedDate)->format('l');
        } else {
            // Agar 1W ya 1M hai toh From Date se To Date ke beech ke saare days nikal lo
            $period = CarbonPeriod::create($fromDate, $toDate);
            foreach ($period as $date) {
                $dayNames[] = $date->format('l');
            }
            $dayNames = array_unique($dayNames);
        }

        // Vendor ke saare catalogs fetch karo aur items ke through filter lagao
        $catalogs = TiffinCatalog::with('items')
            ->where('vendor_id', $userId)
            ->get()
            ->filter(function($catalog) use ($dayNames, $mealTypes) {
                // Check karo ki catalog ke items mein se koi bhi item selected days ya meal types se match karta hai ya nahi
                foreach($catalog->items as $item) {
                    $matchDay = empty($dayNames) || in_array($item->day, $dayNames);
                    $matchMeal = empty($mealTypes) || in_array(strtolower($item->meal_type), array_map('strtolower', $mealTypes));
                    
                    if($matchDay && $matchMeal) {
                        return true;
                    }
                }
                return false;
            });

        $dayName = Carbon::parse($selectedDate)->format('l');

        return view('vendor.restaurant.proceed-tiffin.index', compact(
            'catalogs', 'duration', 'selectedDate', 'fromDate', 'toDate', 'mealTypes', 'dayName', 'dayNames'
        ));
    }
}