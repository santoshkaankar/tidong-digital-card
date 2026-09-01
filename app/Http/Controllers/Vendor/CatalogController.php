<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vendor\Catalog;
use App\Models\Vendor\VendorItem;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CatalogController extends Controller
{
    // 1. कैटलॉग की लिस्ट और क्रिएशन फॉर्म वाला पेज
    public function index()
    {
        $userId = Auth::id();
        $catalogs = Catalog::where('user_id', $userId)->latest()->get();
        
        // वेंडर के एक्टिव आइटम्स फ़ेच करें
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
            // Update Existing
            $catalog = Catalog::where('user_id', Auth::id())->findOrFail($catalogId);
            $catalog->update([
                'address' => $request->address,
                'item_ids' => $request->items ?? [],
            ]);
            $msg = 'कैटलॉग अपडेट कर दिया गया है!';
        } else {
            // Create New
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

    // 5. QR कोड View Page (Vendor Name & Direct Share)
    public function showQr($id)
    {
        $catalog = Catalog::where('user_id', Auth::id())->findOrFail($id);
        $vendor = Auth::user();

        return view('vendor.catalogs.qr_view', compact('catalog', 'vendor'));
    }

    // 6. कस्टमर द्वारा ऑर्डर सबमिट / अपडेट करना (Live Order API)
    public function placeOrder(Request $request)
    {
        $request->validate([
            'catalog_id' => 'required|exists:catalogs,id',
            'cart' => 'required|array',
        ]);

        $catalog = Catalog::findOrFail($request->catalog_id);

        // नोट: यहाँ आप अपने ऑर्डर टेबल में डेटा सेव करवा सकते हैं या किचन डैशबोर्ड पर लाइव अलर्ट भेज सकते हैं।

        return response()->json([
            'success' => true,
            'message' => 'आपका ऑर्डर किचन में भेज दिया गया है!'
        ]);
    }
}