<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Restaurant\RestaurantCategory;
use App\Models\Restaurant\RestaurantItem;
use App\Models\Restaurant\RestaurantOrder;
use App\Models\Restaurant\RestaurantOrderItem;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RestaurantController extends Controller
{
    public function index(Request $request)
{
    $search = $request->input('search');
    $type = $request->input('type');

    $query = User::query();

    // STRICT FILTER: Sirf Restaurant Business Type ya Restaurant Role vale users hi aayenge
    $query->where(function($q) {
        $q->where('business_type', 'restaurant')
          ->orWhere('role', 'restaurant');
    });

    // Search Filter (Name, City, State, Business Name)
    if (!empty($search)) {
        $query->where(function($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
              ->orWhere('username', 'LIKE', "%{$search}%");

            if (Schema::hasColumn('users', 'business_name')) {
                $q->orWhere('business_name', 'LIKE', "%{$search}%");
            }
            if (Schema::hasColumn('users', 'city')) {
                $q->orWhere('city', 'LIKE', "%{$search}%");
            }
            if (Schema::hasColumn('users', 'state')) {
                $q->orWhere('state', 'LIKE', "%{$search}%");
            }
        });
    }

    // Food Type Filter (Veg / Non-Veg)
    if (!empty($type) && $type !== 'all') {
        if (Schema::hasColumn('users', 'food_type')) {
            if ($type === 'pureveg' || $type === 'veg') {
                $query->whereIn('food_type', ['pure_veg', 'veg', 'pureveg']);
            } elseif ($type === 'nonveg') {
                $query->whereIn('food_type', ['non_veg', 'nonveg']);
            }
        }
    }

    $restaurants = $query->latest()->paginate(12)->withQueryString();

    return view('member.restaurant.index', compact('restaurants', 'search', 'type'));
}

    public function show($id)
    {
        $restaurant = User::findOrFail($id);

        // Fetch categories
        $categories = RestaurantCategory::query();
        if (Schema::hasColumn('restaurant_categories', 'user_id')) {
            $categories->where('user_id', $id);
        } elseif (Schema::hasColumn('restaurant_categories', 'restaurant_id')) {
            $categories->where('restaurant_id', $id);
        }
        $categories = $categories->get();

        // Fetch food items
        $itemsQuery = RestaurantItem::query();
        if (Schema::hasColumn('restaurant_items', 'user_id')) {
            $itemsQuery->where('user_id', $id);
        } elseif (Schema::hasColumn('restaurant_items', 'restaurant_id')) {
            $items = $itemsQuery->where('restaurant_id', $id);
        }
        $items = $itemsQuery->where('status', 1)->get();

        return view('member.restaurant.show', compact('restaurant', 'categories', 'items'));
    }

    // NAYA ADDED: Dashboard Live Order Placement Method
    // app/Http/Controllers/Member/RestaurantController.php

public function placeOrder(Request $request)
{
    try {
        $request->validate([
            'restaurant_id' => 'required|exists:users,id',
            'items'         => 'required|array|min:1',
            'items.*.id'    => 'required|exists:restaurant_items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $order = \Illuminate\Support\Facades\DB::transaction(function () use ($request) {
            $subTotal = 0;

            $order = \App\Models\Restaurant\RestaurantOrder::create([
                'user_id'       => auth()->id(),                     // Customer User ID
                'vendor_id'     => $request->restaurant_id,          // Restaurant / Vendor User ID
                'order_number'  => 'ORD-' . strtoupper(\Illuminate\Support\Str::random(6)),
                'order_type'    => 'online',                          // Online / Dashboard Order
                'sub_total'     => 0,
                'total_amount'  => 0,
                'status'        => 'pending',                         // Vendor KDS me WAITING dikhayega
                'payment_status'=> 'unpaid',
            ]);

            foreach ($request->items as $itemData) {
                $item = \App\Models\Restaurant\RestaurantItem::findOrFail($itemData['id']);
                $itemSubtotal = $item->price * $itemData['quantity'];
                $itemName = $item->name ?? 'Food Item';

                \App\Models\Restaurant\RestaurantOrderItem::create([
                    'order_id'       => $order->id,
                    'item_id'        => $item->id,
                    'item_name'      => $itemName,
                    'quantity'       => $itemData['quantity'],
                    'price'          => $item->price,
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
            'message' => 'Order successfully kitchen me bhej diya gaya hai!',
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
}