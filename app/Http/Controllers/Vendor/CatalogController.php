<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vendor\Catalog;
use App\Models\Vendor\VendorItem;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CatalogController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $catalogs = Catalog::where('user_id', $userId)->latest()->get();
        
        $menuItems = VendorItem::where('user_id', $userId)
            ->where(function($q) {
                $q->where('status', 'active')
                  ->orWhere('is_available', true);
            })->get();

        return view('vendor.catalogs.index', compact('catalogs', 'menuItems'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'address' => 'required|string|max:255',
            'items' => 'nullable|array'
        ]);

        $catalogId = $request->input('catalog_id');

        if ($catalogId) {
            $catalog = Catalog::where('user_id', Auth::id())->findOrFail($catalogId);
            $catalog->update([
                'address' => $request->address,
                'item_ids' => $request->items ?? [],
            ]);
            $msg = 'कैटलॉग अपडेट कर दिया गया है!';
        } else {
            $slug = Str::slug($request->address) . '-' . Str::random(5);
            $catalog = Catalog::create([
                'user_id' => Auth::id(),
                'address' => $request->address,
                'slug' => $slug,
                'item_ids' => $request->items ?? [],
            ]);
            $msg = 'नया कैटलॉग सफलतापूर्वक बन गया है!';
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => $msg, 'catalog' => $catalog]);
        }

        return redirect()->back()->with('success', $msg);
    }

    public function destroy($id)
    {
        Catalog::where('user_id', Auth::id())->where('id', $id)->delete();
        return redirect()->back()->with('success', 'कैटलॉग हटा दिया गया है!');
    }

    public function showPublicCatalog($slug)
    {
        $catalog = Catalog::where('slug', $slug)->firstOrFail();
        $items = VendorItem::whereIn('id', $catalog->item_ids ?? [])->get();
        $vendor = User::find($catalog->user_id);

        $activeOrderId = session('active_guest_order_id');
        $activeOrder = null;
        if ($activeOrderId) {
            $activeOrder = DB::table('orders')->where('id', $activeOrderId)->where('status', '!=', 'completed')->first();
        }

        return view('vendor.catalogs.public', compact('catalog', 'items', 'vendor', 'activeOrder'));
    }

    public function showQr($id)
    {
        $catalog = Catalog::where('user_id', Auth::id())->findOrFail($id);
        $vendor = Auth::user();

        return view('vendor.catalogs.qr_view', compact('catalog', 'vendor'));
    }

    public function placeOrder(Request $request)
    {
        $request->validate([
            'catalog_id' => 'required|exists:catalogs,id',
            'cart' => 'required|array',
        ]);

        $catalog = Catalog::findOrFail($request->catalog_id);

        $totalAddAmount = 0;
        foreach ($request->cart as $item) {
            $totalAddAmount += ($item['qty'] * $item['price']);
        }

        DB::beginTransaction();
        try {
            $orderId = session('active_guest_order_id');
            $existingOrder = null;

            if ($orderId) {
                $existingOrder = DB::table('orders')->where('id', $orderId)->where('status', '!=', 'completed')->first();
            }

            if ($existingOrder) {
                DB::table('orders')->where('id', $orderId)->update([
                    'total_amount' => $existingOrder->total_amount + $totalAddAmount,
                    'updated_at'   => now(),
                ]);
            } else {
                $orderId = DB::table('orders')->insertGetId([
                    'user_id'        => $catalog->user_id,
                    'menu_id'        => $catalog->id,
                    'table_or_room'  => $catalog->address,
                    'location_label' => $catalog->address,
                    'status'         => 'running',
                    'payment_mode'   => 'cash',
                    'payment_status' => 'unpaid',
                    'total_amount'   => $totalAddAmount,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);

                session([
                    'active_guest_order_id' => $orderId,
                    'active_catalog_slug'  => $catalog->slug,
                    'active_table_name'    => $catalog->address
                ]);
            }

            foreach ($request->cart as $item) {
                $existingItem = DB::table('order_items')
                    ->where('order_id', $orderId)
                    ->where('item_id', $item['id'])
                    ->first();

                if ($existingItem) {
                    DB::table('order_items')->where('id', $existingItem->id)->update([
                        'quantity'   => $existingItem->quantity + $item['qty'],
                        'updated_at' => now()
                    ]);
                } else {
                    DB::table('order_items')->insert([
                        'order_id'   => $orderId,
                        'menu_id'    => $catalog->id,
                        'item_id'    => $item['id'],
                        'item_name'  => $item['name'],
                        'quantity'   => $item['qty'],
                        'price'      => $item['price'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success'      => true,
                'order_id'     => $orderId,
                'redirect_url' => route('guest.order.status', $orderId),
                'message'      => 'ऑर्डर किचन में भेज दिया गया है!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'ऑर्डर सेव करने में त्रुटि: ' . $e->getMessage()
            ], 500);
        }
    }

    public function guestOrderStatus($orderId)
    {
        $order = DB::table('orders')->where('id', $orderId)->first();
        if (!$order) {
            return redirect('/')->with('error', 'ऑर्डर नहीं मिला');
        }

        $items = DB::table('order_items')->where('order_id', $orderId)->get();
        $catalog = Catalog::find($order->menu_id);
        $vendor = User::find($order->user_id);

        return view('vendor.catalogs.guest_status', compact('order', 'items', 'catalog', 'vendor'));
    }

    public function vacateGuestTable(Request $request, $orderId)
    {
        $request->validate([
            'payment_mode' => 'required|in:cash,upi_online'
        ]);

        $paymentMode = $request->payment_mode;

        if ($paymentMode === 'cash') {
            DB::table('orders')->where('id', $orderId)->update([
                'status' => 'completed',
                'payment_mode' => 'cash',
                'payment_status' => 'unpaid',
                'updated_at' => now()
            ]);
            $message = 'कैश पेमेंट का अनुरोध दर्ज कर लिया गया है। कृपया काउंटर/वेटर को ₹' . number_format($request->total_amount, 2) . ' का भुगतान करें। धन्यवाद!';
        } else {
            DB::table('orders')->where('id', $orderId)->update([
                'status' => 'completed',
                'payment_mode' => 'upi_online',
                'payment_status' => 'paid',
                'updated_at' => now()
            ]);
            $message = 'ऑनलाइन भुगतान की पुष्टि हो गई है। आपकी टेबल खाली कर दी गई है। धन्यवाद!';
        }

        session()->forget(['active_guest_order_id', 'active_catalog_slug', 'active_table_name']);

        return redirect()->route('catalogs.public', $request->catalog_slug ?? '')
                         ->with('success', $message);
    }
}