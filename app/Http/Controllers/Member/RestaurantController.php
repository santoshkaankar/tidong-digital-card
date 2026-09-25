<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Restaurant\RestaurantCategory;
use App\Models\Restaurant\RestaurantItem;
use App\Models\Restaurant\RestaurantCustomItem;
use App\Models\Restaurant\TiffinCatalog;
use App\Models\Restaurant\ProceedTiffinOrder;
use App\Models\Restaurant\RestaurantOrder;
use App\Models\Restaurant\RestaurantOrderItem;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RestaurantController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('q') ?? $request->input('search');
        $type   = $request->input('type');
        $lat    = $request->input('lat');
        $lng    = $request->input('lng');
        $pincode = $request->input('pincode');

        // Pincode se Lat/Lng autodetect (Pincodes table lookup)
        if (!empty($pincode) && (empty($lat) || empty($lng))) {
            $pinData = DB::table('pincodes')->where('pincode', $pincode)->first();
            if ($pinData && !empty($pinData->latitude) && !empty($pinData->longitude)) {
                $lat = $pinData->latitude;
                $lng = $pinData->longitude;
            }
        }

        $query = User::query();

        // 1. STRICT ROLE / BUSINESS FILTER
        $query->where(function($q) {
            $q->where('business_type', 'restaurant')
              ->orWhere('role', 'restaurant');
        });

        // DYNAMIC MULTI-SEARCH
if (!empty($search)) {
    $query->where(function($q) use ($search) {
        // 1. Users table ke fields
        $q->where('name', 'LIKE', "%{$search}%")
          ->orWhere('username', 'LIKE', "%{$search}%")
          ->orWhere('city', 'LIKE', "%{$search}%")
          ->orWhere('state', 'LIKE', "%{$search}%")
          ->orWhere('pincode', 'LIKE', "%{$search}%")
          ->orWhere('area', 'LIKE', "%{$search}%")
          ->orWhere('address', 'LIKE', "%{$search}%");

        // 2. Agar pincodes table se match karana hai (pincodes table ki columns)
        $q->orWhereIn('pincode', function($subQuery) use ($search) {
            $subQuery->select('pincode')
                     ->from('pincodes')
                     ->where('office_name', 'LIKE', "%{$search}%")
                     ->orWhere('district', 'LIKE', "%{$search}%")
                     ->orWhere('state_name', 'LIKE', "%{$search}%");
        });
    });
}
        

        // 3. VEG / NON-VEG FILTER
        if (!empty($type) && $type !== 'all') {
            if (Schema::hasColumn('users', 'food_type')) {
                if (in_array($type, ['pureveg', 'veg'])) {
                    $query->whereIn('food_type', ['pure_veg', 'veg', 'pureveg']);
                } elseif ($type === 'nonveg') {
                    $query->whereIn('food_type', ['non_veg', 'nonveg']);
                }
            }
        }

        // 4. NEARBY DISTANCE SORTING (PostgreSQL Compatible Haversine Formula)
        if (!empty($lat) && !empty($lng)) {
            $query->selectRaw("*, ( 6371 * acos( cos( radians(?) ) * cos( radians( COALESCE(latitude, 0) ) ) * cos( radians( COALESCE(longitude, 0) ) - radians(?) ) + sin( radians(?) ) * sin( radians( COALESCE(latitude, 0) ) ) ) ) AS distance", [$lat, $lng, $lat])
                  ->orderBy('distance', 'asc');
        } else {
            $query->latest();
        }

        $restaurants = $query->paginate(12)->appends($request->all());

        return view('member.restaurant.index', compact('restaurants', 'search', 'type', 'lat', 'lng', 'pincode'));
    }

    public function show($id)
    {
        $restaurant = User::findOrFail($id);

        // 1. Fetch categories
        $categories = RestaurantCategory::query();
        if (Schema::hasColumn('restaurant_categories', 'user_id')) {
            $categories->where('user_id', $id);
        } elseif (Schema::hasColumn('restaurant_categories', 'restaurant_id')) {
            $categories->where('restaurant_id', $id);
        }
        $categories = $categories->get();

        // 2. Fetch standard items (Supabase Fix: status = true)
        $itemsQuery = RestaurantItem::query();
        if (Schema::hasColumn('restaurant_items', 'user_id')) {
            $itemsQuery->where('user_id', $id);
        } elseif (Schema::hasColumn('restaurant_items', 'restaurant_id')) {
            $itemsQuery->where('restaurant_id', $id);
        }
        $globalItems = $itemsQuery->where('status', true)->get();

        // 3. Fetch custom items / Thalis (Supabase Fix: is_available = true)
        $customItems = RestaurantCustomItem::where('user_id', $id)
            ->where('is_available', true)
            ->get();

        // 4. Today's Tiffin Schedule
        $todayDay = Carbon::now()->format('l');
        $todayTiffins = TiffinCatalog::with(['items' => function($q) use ($todayDay) {
            $q->where('day', $todayDay);
        }])
        ->where('vendor_id', $id)
        ->get()
        ->filter(function($catalog) {
            return $catalog->items->count() > 0;
        });

        return view('member.restaurant.show', compact('restaurant', 'categories', 'globalItems', 'customItems', 'todayTiffins', 'todayDay'));
    }

    // Live Delivery & Dining Order Placement
    public function placeOrder(Request $request)
    {
        try {
            $request->validate([
                'restaurant_id'    => 'required|exists:users,id',
                'order_type'       => 'required|in:delivery,dine_in,takeaway',
                'delivery_address' => 'required_if:order_type,delivery|nullable|string',
                'pincode'          => 'nullable|string',
                'items'            => 'required|array|min:1',
            ]);

            $order = DB::transaction(function () use ($request) {
                $subTotal = 0;

                $order = RestaurantOrder::create([
                    'user_id'          => auth()->id(),
                    'vendor_id'        => $request->restaurant_id,
                    'order_number'     => 'ORD-' . strtoupper(Str::random(6)),
                    'order_type'       => $request->order_type,
                    'delivery_address' => $request->delivery_address,
                    'pincode'          => $request->pincode,
                    'sub_total'        => 0,
                    'total_amount'     => 0,
                    'status'           => 'pending',
                    'payment_status'   => 'unpaid',
                    'delivery_status'  => $request->order_type === 'delivery' ? 'assigning_delivery_boy' : 'not_applicable'
                ]);

                foreach ($request->items as $itemData) {
                    $item = RestaurantItem::find($itemData['id']);
                    $price = $item ? $item->price : ($itemData['price'] ?? 0);
                    $itemSubtotal = $price * $itemData['quantity'];
                    $itemName = $item ? ($item->name ?? 'Food Item') : ($itemData['name'] ?? 'Custom Dish');

                    RestaurantOrderItem::create([
                        'order_id'       => $order->id,
                        'item_id'        => $itemData['id'] ?? null,
                        'item_name'      => $itemName,
                        'quantity'       => $itemData['quantity'],
                        'price'          => $price,
                        'subtotal'       => $itemSubtotal,
                        'kitchen_status' => 'cooking'
                    ]);

                    $subTotal += $itemSubtotal;
                }

                $order->sub_total = $subTotal;
                $order->total_amount = $subTotal;
                $order->save();

                return $order;
            });

            return response()->json([
                'success' => true,
                'message' => 'Order successfully place ho gaya hai!',
                'order_id' => $order->id,
                'total_amount' => $order->total_amount
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Tiffin Pre-Booking Method
    public function bookTiffin(Request $request, $id)
    {
        try {
            $request->validate([
                'duration'   => 'required|in:1d,1w,1m,custom',
                'from_date'  => 'required|date',
                'to_date'    => 'nullable|date',
                'meal_types' => 'required|array',
                'catalog_id' => 'required|exists:tiffin_catalogs,id',
            ]);

            $fromDate = $request->from_date;
            $toDate = $request->to_date;

            if ($request->duration === '1d') {
                $toDate = $fromDate;
            } elseif ($request->duration === '1w') {
                $toDate = Carbon::parse($fromDate)->addDays(7)->format('Y-m-d');
            } elseif ($request->duration === '1m') {
                $toDate = Carbon::parse($fromDate)->addMonth()->format('Y-m-d');
            }

            $order = ProceedTiffinOrder::create([
                'vendor_id'         => $id,
                'customer_name'     => auth()->user()->name ?? 'Customer',
                'customer_mobile'   => auth()->user()->mobile ?? auth()->user()->email ?? '',
                'duration'          => $request->duration,
                'from_date'         => $fromDate,
                'to_date'           => $toDate,
                'meal_types'        => $request->meal_types,
                'selected_catalogs' => [$request->catalog_id],
                'status'            => 'pending',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tiffin Booking request submit ho gayi hai!',
                'order_id' => $order->id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}