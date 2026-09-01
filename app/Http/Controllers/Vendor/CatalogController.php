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
    // 1. कैटलॉग की लिस्ट और क्रिएशन फॉर्म वाला पेज
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

    // 2. नया कैटलॉग सेव / अपडेट करना
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

    // 3. कैटलॉग डिलीट करना
    public function destroy($id)
    {
        Catalog::where('user_id', Auth::id())->where('id', $id)->delete();
        return redirect()->back()->with('success', 'कैटलॉग हटा दिया गया है!');
    }

    // 4. कस्टमर के लिए पब्लिक कैटलॉग मेनू पेज
    public function showPublicCatalog($slug)
    {
        $catalog = Catalog::where('slug', $slug)->firstOrFail();
        $items = VendorItem::whereIn('id', $catalog->item_ids ?? [])->get();
        $vendor = User::find($catalog->user_id);

        return view('vendor.catalogs.public', compact('catalog', 'items', 'vendor'));
    }

    // 5. QR कोड View Page
    public function showQr($id)
    {
        $catalog = Catalog::where('user_id', Auth::id())->findOrFail($id);
        $vendor = Auth::user();

        return view('vendor.catalogs.qr_view', compact('catalog', 'vendor'));
    }

    // 6. लाइव ऑर्डर डेटाबेस सेविंग (Database Insert & Kitchen Live Sync)
    public function placeOrder(Request $request)
    {
        $request->validate([
            'catalog_id' => 'required|exists:catalogs,id',
            'cart' => 'required|array',
        ]);

        $catalog = Catalog::findOrFail($request->catalog_id);

        $totalAmount = 0;
        foreach ($request->cart as $item) {
            $totalAmount += ($item['qty'] * $item['price']);
        }

        DB::beginTransaction();
        try {
            // orders टेबल में सीधा एंट्री करें (अब menu_id nullable है)
            $orderId = DB::table('orders')->insertGetId([
                'user_id'        => $catalog->user_id,
                'menu_id'        => $catalog->id,
                'table_or_room'  => $catalog->address,
                'location_label' => $catalog->address,
                'status'         => 'pending',
                'payment_mode'   => 'cash',
                'payment_status' => 'unpaid',
                'total_amount'   => $totalAmount,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

            foreach ($request->cart as $item) {
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

            DB::commit();

            return response()->json([
                'success'  => true,
                'order_id' => $orderId,
                'message'  => 'आपका ऑर्डर किचन में भेज दिया गया है!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'ऑर्डर सेव करने में त्रुटि: ' . $e->getMessage()
            ], 500);
        }
    }
}