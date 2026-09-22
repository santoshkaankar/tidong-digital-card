<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Restaurant\TiffinCatalog;
use App\Models\Restaurant\ProceedTiffinOrder;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class ProceedTiffinController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();
        
        $duration = $request->input('duration', '1d');
        $selectedDate = $request->input('date', date('Y-m-d'));
        $fromDate = $request->input('from_date', date('Y-m-d'));
        $toDate = $request->input('to_date', date('Y-m-d', strtotime('+7 days')));
        $mealTypes = $request->input('meal_types', ['breakfast', 'lunch', 'snacks', 'dinner']); 

        $dayNames = [];

        if ($duration == '1d' || $duration == 'custom') {
            $dayNames[] = Carbon::parse($selectedDate)->format('l');
        } else {
            $period = CarbonPeriod::create($fromDate, $toDate);
            foreach ($period as $date) {
                $dayNames[] = $date->format('l');
            }
            $dayNames = array_unique($dayNames);
        }

        $catalogs = TiffinCatalog::with('items')
            ->where('vendor_id', $userId)
            ->get()
            ->filter(function($catalog) use ($dayNames, $mealTypes) {
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

    // AJAX Customer Lookup by Mobile Number
    public function customerLookup(Request $request)
{
    $identifier = $request->query('mobile') ?? $request->query('email') ?? $request->query('username');

    if (!$identifier) {
        return response()->json(['exists' => false]);
    }

    // Unique fields par search karein (mobile, email, username)
    $user = \App\Models\User::where('mobile', $identifier)
        ->orWhere('email', $identifier)
        ->orWhere('username', $identifier)
        ->first();

    if ($user) {
        return response()->json([
            'exists' => true,
            'name' => $user->name,
            'mobile' => $user->mobile ?? null,
            'email' => $user->email ?? null,
            'username' => $user->username ?? null,
        ]);
    }

    return response()->json(['exists' => false]);
}

    public function store(Request $request)
    {
        $userId = auth()->id();
        
        try {
            $request->validate([
                'customer_name'     => 'required|string|max:255',
                'customer_mobile'   => 'required|string|max:15',
                'selected_catalogs' => 'required|array',
            ]);

            $selectedCatalogs = $request->input('selected_catalogs', []);

            if (empty($selectedCatalogs)) {
                return redirect()->back()->with('error', 'Kam se kam ek catalog select karna zaroori hai!');
            }

            // Dates ko handle karna taaki koi bhi date NULL na rahe
            $fromDate = $request->input('from_date') ?: date('Y-m-d');
            $toDate = $request->input('to_date') ?: $fromDate;
            
            if ($toDate < $fromDate) {
                $toDate = $fromDate;
            }

            // 1. Check if user already exists by mobile number
            $user = User::where('mobile', $request->customer_mobile)->first();

            if (!$user) {
                $sponsorRefId = 'TDMS6395GSSS'; // Meenu Sharma's ID
                $sponsor = User::where('referral_id', $sponsorRefId)->first();
                
                $sponsorId = $sponsor ? $sponsor->id : null;
                $parentId = $sponsorId;
                $position = 'left';

                if ($sponsorId) {
                    $currentId = $sponsorId;
                    while (true) {
                        $existingChild = User::where('parent_id', $currentId)
                                             ->where('position', 'left')
                                             ->first();
                        if (!$existingChild) {
                            $parentId = $currentId;
                            $position = 'left';
                            break;
                        }
                        $currentId = $existingChild->id;
                    }
                }

                do {
                    $referralId = 'TABS' . strtoupper(Str::random(8));
                } while (User::where('referral_id', $referralId)->exists());

                // Create new user using mobile number as primary identifier
                $user = User::create([
                    'name'          => $request->customer_name,
                    'username'      => 'tiffin_' . rand(10000, 99999),
                    'referral_id'   => $referralId,
                    'sponsor_id'    => $sponsorId,
                    'parent_id'     => $parentId,
                    'position'      => $position,
                    'slug'          => Str::slug($request->customer_name) . '-' . rand(1000, 9999),
                    'email'         => 'tiffin_' . $request->customer_mobile . '@tidong.in', // unique dummy email based on mobile
                    'mobile'        => $request->customer_mobile,
                    'password'      => Hash::make('12345678'),
                    'role'          => 'member',
                ]);

                // Binary tree count increment logic
                if ($parentId) {
                    $currParentId = $parentId;
                    $currPos = 'left';
                    while ($currParentId) {
                        $parentUser = User::find($currParentId);
                        if (!$parentUser) break;
                        if ($currPos === 'left') {
                            $parentUser->increment('left_count');
                        } else {
                            $parentUser->increment('right_count');
                        }
                        $currPos = $parentUser->position;
                        $currParentId = $parentUser->parent_id;
                    }
                }
            } else {
                // Agar user pehle se hai, toh naam update ya match kar sakte hain agar zaroorat ho
                $user->update(['name' => $request->customer_name]);
            }

            // 2. Save Proceed Tiffin Order
            ProceedTiffinOrder::create([
                'vendor_id'         => $userId,
                'customer_name'     => $request->customer_name,
                'customer_mobile'   => $request->customer_mobile,
                'duration'          => $request->input('duration', '1d'),
                'from_date'         => $fromDate,
                'to_date'           => $toDate,
                'meal_types'        => $request->input('meal_types', []),
                'selected_catalogs' => $selectedCatalogs,
                'status'            => 'pending',
            ]);

            return redirect()->route('vendor.restaurant.proceed-tiffin.list')->with('success', 'Tiffin successfully save ho gaya!');

        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }
    public function listSaved()
    {
        $userId = auth()->id();
        $savedOrders = ProceedTiffinOrder::where('vendor_id', $userId)->latest()->get();

        return view('vendor.restaurant.proceed-tiffin.list', compact('savedOrders'));
    }

    public function show(Request $request, $id)
    {
        $order = ProceedTiffinOrder::findOrFail($id);
        
        $fromDate = $order->from_date ?? $order->date ?? date('Y-m-d');
        $toDate = $order->to_date ?? $fromDate;

        $period = CarbonPeriod::create($fromDate, $toDate);
        $calendarDates = [];

        $filterDate = $request->input('filter_date');
        $selectedMeal = $request->input('meal_type');

        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            
            if ($filterDate && $filterDate != $dateStr) {
                continue;
            }

            $calendarDates[] = [
                'date' => $dateStr,
                'day_name' => $date->format('D'),
                'formatted_date' => $date->format('d M Y'),
                'status' => 'pending' 
            ];
        }

        return view('vendor.restaurant.proceed-tiffin.show', compact('order', 'calendarDates', 'filterDate', 'selectedMeal'));
    }

    public function scheduleView(Request $request)
{
    $userId = auth()->id();
    
    // Status pending ya null dono ko handle karega taaki purane orders bhi dikhein
    $query = ProceedTiffinOrder::where('vendor_id', $userId)
        ->where(function($q) {
            $q->where('status', 'pending')->orWhereNull('status');
        });

    // Date Range Overlapping Filter
    if ($request->filled('from_date') && $request->filled('to_date')) {
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $query->where(function($q) use ($fromDate, $toDate) {
            $q->whereDate('from_date', '<=', $toDate)
              ->whereDate('to_date', '>=', $fromDate);
        });
    } elseif ($request->filled('from_date')) {
        $query->whereDate('to_date', '>=', $request->input('from_date'));
    } elseif ($request->filled('to_date')) {
        $query->whereDate('from_date', '<=', $request->input('to_date'));
    }

    // Meal Type Filter
    if ($request->filled('meal_type') && $request->input('meal_type') != 'all') {
        $meal = $request->input('meal_type');
        $query->whereJsonContains('meal_types', $meal);
    }

    // Search query via Dropdown (Order ID or Name)
    if ($request->filled('search_query')) {
        $search = $request->input('search_query');
        $query->where(function($q) use ($search) {
            $q->where('id', $search)
              ->orWhere('customer_name', 'LIKE', "%{$search}%");
        });
    }

    $orders = $query->latest()->get();

    // Dropdown ke liye pending orders list
    $pendingOrders = ProceedTiffinOrder::where('vendor_id', $userId)
        ->where(function($q) {
            $q->where('status', 'pending')->orWhereNull('status');
        })
        ->select('id', 'customer_name', 'customer_mobile')
        ->get();

    return view('vendor.restaurant.proceed-tiffin.schedule', compact('orders', 'pendingOrders'));
}
}
