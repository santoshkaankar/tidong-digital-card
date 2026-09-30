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
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Carbon\Carbon;

class RestaurantController extends Controller
{
    // 1. Restaurant Listing Page
    public function index(Request $request)
    {
        $search  = $request->input('q') ?? $request->input('search');
        $type    = $request->input('type');
        $lat     = $request->input('lat');
        $lng     = $request->input('lng');
        $pincode = $request->input('pincode');

        if (!empty($pincode) && (empty($lat) || empty($lng))) {
            $pinData = DB::table('pincodes')->where('pincode', $pincode)->first();
            if ($pinData && !empty($pinData->latitude) && !empty($pinData->longitude)) {
                $lat = $pinData->latitude;
                $lng = $pinData->longitude;
            }
        }

        $query = User::query();

        $query->where(function($q) {
            $q->where('business_type', 'restaurant')
              ->orWhere('role', 'restaurant');
        });

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('username', 'LIKE', "%{$search}%")
                  ->orWhere('city', 'LIKE', "%{$search}%")
                  ->orWhere('state', 'LIKE', "%{$search}%")
                  ->orWhere('pincode', 'LIKE', "%{$search}%")
                  ->orWhere('area', 'LIKE', "%{$search}%")
                  ->orWhere('address', 'LIKE', "%{$search}%");

                $q->orWhereIn('pincode', function($subQuery) use ($search) {
                    $subQuery->select('pincode')
                             ->from('pincodes')
                             ->where('office_name', 'LIKE', "%{$search}%")
                             ->orWhere('district', 'LIKE', "%{$search}%")
                             ->orWhere('state_name', 'LIKE', "%{$search}%");
                });
            });
        }

        if (!empty($type) && $type !== 'all') {
            if (Schema::hasColumn('users', 'food_type')) {
                if (in_array($type, ['pureveg', 'veg'])) {
                    $query->whereIn('food_type', ['pure_veg', 'veg', 'pureveg']);
                } elseif ($type === 'nonveg') {
                    $query->whereIn('food_type', ['non_veg', 'nonveg']);
                }
            }
        }

        if (!empty($lat) && !empty($lng)) {
            $query->selectRaw("*, ( 6371 * acos( cos( radians(?) ) * cos( radians( COALESCE(latitude, 0) ) ) * cos( radians( COALESCE(longitude, 0) ) - radians(?) ) + sin( radians(?) ) * sin( radians( COALESCE(latitude, 0) ) ) ) ) AS distance", [$lat, $lng, $lat])
                  ->orderBy('distance', 'asc');
        } else {
            $query->latest();
        }

        $restaurants = $query->paginate(12)->appends($request->all());

        return view('member.restaurant.index', compact('restaurants', 'search', 'type', 'lat', 'lng', 'pincode'));
    }

    // 2. Restaurant Detail Page / Menu
    public function show($id)
    {
        $restaurant = User::findOrFail($id);

        $categories = RestaurantCategory::query();
        if (Schema::hasColumn('restaurant_categories', 'user_id')) {
            $categories->where('user_id', $id);
        } elseif (Schema::hasColumn('restaurant_categories', 'restaurant_id')) {
            $categories->where('restaurant_id', $id);
        }
        $categories = $categories->get();

        $itemsQuery = RestaurantItem::query();
        if (Schema::hasColumn('restaurant_items', 'user_id')) {
            $itemsQuery->where('user_id', $id);
        } elseif (Schema::hasColumn('restaurant_items', 'restaurant_id')) {
            $itemsQuery->where('restaurant_id', $id);
        }
        $globalItems = $itemsQuery->where('status', true)->get();

        $customItems = RestaurantCustomItem::where('user_id', $id)
            ->where('is_available', true)
            ->get();

        $todayDay = Carbon::now()->format('l');
        $todayTiffins = TiffinCatalog::with(['items' => function($q) use ($todayDay) {
            $q->where('day', $todayDay);
        }])
        ->where('vendor_id', $id)
        ->get()
        ->filter(function($catalog) {
            return $catalog->items->count() > 0;
        });

        $savedAddresses = [];
        if (auth()->check()) {
            if (Schema::hasTable('user_addresses')) {
                $savedAddresses = DB::table('user_addresses')->where('user_id', auth()->id())->get();
            } elseif (Schema::hasTable('addresses')) {
                $savedAddresses = DB::table('addresses')->where('user_id', auth()->id())->get();
            }
        }

        return view('member.restaurant.show', compact('restaurant', 'categories', 'globalItems', 'customItems', 'todayTiffins', 'todayDay', 'savedAddresses'));
    }

    // 3. Pincode Search Endpoint
    public function searchPincodes(Request $request)
    {
        try {
            $search = trim($request->get('q') ?? $request->get('query') ?? '');

            if (strlen($search) < 2) {
                return response()->json([]);
            }

            $searchTerm = '%' . strtolower($search) . '%';

            $pincodes = DB::table('pincodes')
                ->select(
                    'office_name',
                    'district',
                    DB::raw("COALESCE(state_name, 'Rajasthan') as state"),
                    'pincode'
                )
                ->where(function($q) use ($searchTerm) {
                    $q->where('pincode', 'LIKE', $searchTerm)
                      ->orWhereRaw("LOWER(office_name) LIKE ?", [$searchTerm])
                      ->orWhereRaw("LOWER(district) LIKE ?", [$searchTerm]);
                })
                ->limit(15)
                ->get();

            return response()->json($pincodes);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // 4. Customer Place Order (STRICT BLOCKING FOR UNPAID ONLINE ORDERS)
    public function placeOrder(Request $request)
    {
        try {
            $request->validate([
                'restaurant_id'    => 'required|exists:users,id',
                'order_type'       => 'required|in:delivery,dine_in,takeaway',
                'delivery_address' => 'required_if:order_type,delivery|nullable|string',
                'pincode'          => 'nullable|string',
                'payment_method'   => 'nullable|string',
                'payment_status'   => 'nullable|string',
                'items'            => 'required|array|min:1',
                'tip_amount'       => 'nullable|numeric|min:0',
            ]);

            $paymentMethod = strtolower($request->input('payment_method', 'cod'));
            $paymentStatus = strtolower($request->input('payment_status', 'unpaid'));

            // STRICT CHECK: Agar Online Payment hai aur status 'paid' nahi hai, toh order DB me nahi jayega
            if (!in_array($paymentMethod, ['cash', 'cod']) && $paymentStatus !== 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'Online payment confirm nahi hui hai. Order create nahi kiya ja sakta.'
                ], 400);
            }

            $order = DB::transaction(function () use ($request, $paymentMethod, $paymentStatus) {
                $subTotal = 0;
                $totalTax = 0;

                foreach ($request->items as $itemData) {
                    $cleanId = preg_replace('/[^0-9]/', '', $itemData['id'] ?? 0);
                    $item = RestaurantItem::find($cleanId) ?? RestaurantCustomItem::find($cleanId);

                    $price = isset($itemData['price']) && is_numeric($itemData['price']) 
                        ? floatval($itemData['price']) 
                        : ($item ? floatval($item->price) : 0);

                    $quantity = intval($itemData['quantity'] ?? 1);
                    $itemSubtotal = $price * $quantity;

                    $taxPercent = 0;
                    if ($item) {
                        $rawTax = $item->tax_percent ?? $item->tax_rate ?? $item->tax ?? 0;
                        if (is_object($rawTax)) {
                            $taxPercent = floatval($rawTax->rate ?? $rawTax->tax_percent ?? $rawTax->percent ?? 0);
                        } else {
                            $taxPercent = floatval($rawTax);
                        }
                    }

                    if ($taxPercent <= 0 && isset($itemData['tax_percent'])) {
                        $rawTaxPayload = $itemData['tax_percent'];
                        if (is_object($rawTaxPayload)) {
                            $taxPercent = floatval($rawTaxPayload->rate ?? $rawTaxPayload->tax_percent ?? 0);
                        } else {
                            $taxPercent = floatval($rawTaxPayload);
                        }
                    }

                    if ($taxPercent <= 0) {
                        $taxPercent = 5.00;
                    }

                    $itemBasePrice = $itemSubtotal / (1 + ($taxPercent / 100));
                    $itemTax = $itemSubtotal - $itemBasePrice;

                    $subTotal += $itemSubtotal;
                    $totalTax += $itemTax;
                }

                $deliveryCharge = 0.00;
                if ($request->order_type === 'delivery') {
                    $deliveryCharge = ($subTotal >= 999.00) ? 0.00 : 30.00;
                }

                $tipAmount = floatval($request->input('tip_amount', 0.00));
                $cgst = round($totalTax / 2, 2);
                $sgst = round($totalTax / 2, 2);
                
                $grandTotal = round($subTotal + $deliveryCharge + $tipAmount, 2);

                $formattedAddress = '';
                if ($request->order_type === 'delivery' && !empty($request->delivery_address)) {
                    $formattedAddress = $request->delivery_address;
                    if (!empty($request->pincode) && !str_contains($formattedAddress, $request->pincode)) {
                        $formattedAddress .= " (Pincode: " . $request->pincode . ")";
                    }
                }

                $finalPaymentStatus = in_array($paymentMethod, ['cash', 'cod']) ? 'unpaid' : 'paid';

                $orderData = [
                    'user_id'          => $request->restaurant_id, 
                    'customer_id'      => auth()->id(),            
                    'customer_name'    => auth()->user()->name ?? 'Customer',
                    'customer_phone'   => auth()->user()->mobile ?? auth()->user()->phone ?? null,
                    'order_number'     => 'ORD-' . strtoupper(Str::random(6)),
                    'order_type'       => $request->order_type,
                    'sub_total'        => $subTotal,
                    'tax_amount'       => round($totalTax, 2),
                    'tip_amount'       => $tipAmount,
                    'total_amount'     => $grandTotal,
                    'status'           => 'pending',
                    'payment_status'   => $finalPaymentStatus,
                ];

                if (Schema::hasColumn('restaurant_orders', 'cgst')) {
                    $orderData['cgst'] = $cgst;
                }
                if (Schema::hasColumn('restaurant_orders', 'sgst')) {
                    $orderData['sgst'] = $sgst;
                }
                if (Schema::hasColumn('restaurant_orders', 'delivery_fee')) {
                    $orderData['delivery_fee'] = $deliveryCharge;
                }
                if (Schema::hasColumn('restaurant_orders', 'delivery_charge')) {
                    $orderData['delivery_charge'] = $deliveryCharge;
                }
                if (Schema::hasColumn('restaurant_orders', 'payment_method')) {
                    $orderData['payment_method'] = $paymentMethod;
                }
                if (Schema::hasColumn('restaurant_orders', 'delivery_address')) {
                    $orderData['delivery_address'] = $formattedAddress;
                }
                if (Schema::hasColumn('restaurant_orders', 'pincode')) {
                    $orderData['pincode'] = $request->pincode;
                }
                if (Schema::hasColumn('restaurant_orders', 'notes')) {
                    $orderData['notes'] = $formattedAddress ? "Delivery Address: " . $formattedAddress : ($request->notes ?? '');
                }
                if (Schema::hasColumn('restaurant_orders', 'address')) {
                    $orderData['address'] = $formattedAddress;
                }
                if (Schema::hasColumn('restaurant_orders', 'delivery_status')) {
                    $orderData['delivery_status'] = $request->order_type === 'delivery' ? 'pending' : 'not_applicable';
                }

                $order = RestaurantOrder::create($orderData);

                if (!$order || !$order->id) {
                    throw new \Exception("Failed to generate Order ID.");
                }

                foreach ($request->items as $itemData) {
                    $cleanId = preg_replace('/[^0-9]/', '', $itemData['id'] ?? 0);
                    $item = RestaurantItem::find($cleanId) ?? RestaurantCustomItem::find($cleanId);

                    $itemName = $itemData['name'] 
                             ?? $itemData['item_name'] 
                             ?? ($item ? ($item->name ?? $item->title ?? $item->item_name) : null) 
                             ?? 'Food Item';

                    $price = isset($itemData['price']) && is_numeric($itemData['price']) 
                        ? floatval($itemData['price']) 
                        : ($item ? floatval($item->price) : 0);

                    $quantity = intval($itemData['quantity'] ?? 1);
                    $itemSubtotal = $price * $quantity;

                    $taxPercent = 0;
                    if ($item) {
                        $rawTax = $item->tax_percent ?? $item->tax_rate ?? $item->tax ?? 0;
                        if (is_object($rawTax)) {
                            $taxPercent = floatval($rawTax->rate ?? $rawTax->tax_percent ?? $rawTax->percent ?? 0);
                        } else {
                            $taxPercent = floatval($rawTax);
                        }
                    }

                    if ($taxPercent <= 0 && isset($itemData['tax_percent'])) {
                        $rawTaxPayload = $itemData['tax_percent'];
                        if (is_object($rawTaxPayload)) {
                            $taxPercent = floatval($rawTaxPayload->rate ?? $rawTaxPayload->tax_percent ?? 0);
                        } else {
                            $taxPercent = floatval($rawTaxPayload);
                        }
                    }

                    if ($taxPercent <= 0) {
                        $taxPercent = 5.00;
                    }

                    $itemBasePrice = $itemSubtotal / (1 + ($taxPercent / 100));
                    $itemTax = $itemSubtotal - $itemBasePrice;

                    RestaurantOrderItem::create([
                        'order_id'       => $order->id,
                        'item_id'        => $cleanId ?: null,
                        'item_name'      => $itemName,
                        'quantity'       => $quantity,
                        'price'          => $price,
                        'subtotal'       => $itemSubtotal,
                        'tax_percent'    => $taxPercent,
                        'tax_amount'     => round($itemTax, 2),
                        'batch_number'   => 1,
                        'kitchen_status' => 'pending'
                    ]);
                }

                return $order;
            });

            if (Route::has('member.orders.show')) {
                $redirectUrl = route('member.orders.show', $order->id);
            } else {
                $redirectUrl = url('/member/orders/' . $order->id);
            }

            return response()->json([
                'success'        => true,
                'message'        => 'Order placed successfully!',
                'order_id'       => $order->id,
                'total_amount'   => $order->total_amount,
                'payment_method' => $paymentMethod,
                'redirect_url'   => $redirectUrl
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to place order: ' . $e->getMessage()
            ], 500);
        }
    }

    // 5. Customer Order History / List Page
    public function orderHistory(Request $request)
    {
        $orders = RestaurantOrder::where('customer_id', auth()->id())
            ->where(function($query) {
                $query->whereIn('payment_method', ['cash', 'cod'])
                      ->orWhere(function($q) {
                          $q->whereNotIn('payment_method', ['cash', 'cod'])
                            ->where('payment_status', 'paid');
                      });
            })
            ->latest()
            ->paginate(10);

        return view('member.orders.index', compact('orders'));
    }

    // 6. Book Tiffin Request
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
                'message' => 'Tiffin booking request submitted successfully!',
                'order_id' => $order->id
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to book tiffin: ' . $e->getMessage()
            ], 500);
        }
    }
}