<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VendorCardController extends Controller
{
    // 1. Dashboard / Card Listing
    public function index()
    {
        $userId = auth()->id();
        $masterCard = DB::table('visiting_cards')->where('user_id', $userId)->first();
        
        $cardViews = [];
        if ($masterCard) {
            $cardViews = DB::table('user_card_views')
                ->where('visiting_card_id', $masterCard->id)
                ->where('user_id', $userId)
                ->orderBy('id', 'desc')
                ->get();
        }

        return view('vendor.card.index', compact('masterCard', 'cardViews'));
    }

    // 2. Form View (Master Profile + View Engine)
    public function create()
    {
        $userId = auth()->id();
        $masterCard = DB::table('visiting_cards')->where('user_id', $userId)->first();

        return view('vendor.card.create', compact('masterCard'));
    }

    // 3. Save / Update Master Config (visiting_cards Table)
    public function storeMaster(Request $request)
    {
        $userId = auth()->id();

        $data = $request->only([
            'name', 'nickname', 'business_name', 'designation', 'tagline',
            'phone', 'alt_phone', 'whatsapp', 'gmail', 'website',
            'facebook', 'instagram', 'linkedin', 'youtube', 'telegram',
            'address', 'area', 'city', 'state', 'pincode', 'location_url', 'upi_id'
        ]);

        // Checkbox field visibility toggles (1 or 0)
        $toggles = [
            'show_nickname', 'show_business_name', 'show_designation', 'show_tagline',
            'show_phone', 'show_alt_phone', 'show_whatsapp', 'show_gmail', 'show_website',
            'show_facebook', 'show_instagram', 'show_linkedin', 'show_youtube', 'show_telegram',
            'show_address', 'show_area', 'show_city', 'show_state', 'show_pincode',
            'show_location_url', 'show_upi_id', 'show_photo', 'show_qr_code'
        ];

        foreach ($toggles as $toggle) {
            $data[$toggle] = $request->has($toggle) ? 1 : 0;
        }

        // Image & QR Upload Handling
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('uploads/cards/photos', 'public');
            $data['photo'] = 'storage/' . $photoPath;
        }

        if ($request->hasFile('qr_code')) {
            $qrPath = $request->file('qr_code')->store('uploads/cards/qr', 'public');
            $data['qr_code'] = 'storage/' . $qrPath;
        }

        DB::table('visiting_cards')->updateOrInsert(
            ['user_id' => $userId],
            array_merge($data, ['updated_at' => now()])
        );

        return redirect()->route('vendor.card.create')->with('success', 'Master Profile Config successfully save ho gaya!');
    }

    // 4. Save Custom Card Design (user_card_views Table)
    public function storeCardView(Request $request)
    {
        $userId = auth()->id();
        $masterCard = DB::table('visiting_cards')->where('user_id', $userId)->first();

        if (!$masterCard) {
            return response()->json(['success' => false, 'message' => 'Pehle Master Config save karein.'], 400);
        }

        $cardSlug = 'vc-' . Str::lower(Str::random(8));
        $fullCardNo = '12091-' . str_pad($userId, 6, '0', STR_PAD_LEFT) . '-V' . rand(1, 9);

        // Active Visibility Toggles array into JSON
        $fieldToggles = $request->input('field_toggles', []);

        DB::table('user_card_views')->insert([
            'user_id'             => $userId,
            'visiting_card_id'    => $masterCard->id,
            'card_slug'           => $cardSlug,
            'theme_style'         => $request->input('theme_style', 'default'),
            'theme_category_code' => $request->input('theme_category_code', 'A'),
            'variant_number'      => $request->input('variant_number', 1),
            'full_card_no'        => $fullCardNo,
            'font_family'         => $request->input('font_family', "'Poppins', sans-serif"),
            'icon_style'          => $request->input('icon_style', 'solid'),
            'icon_display_mode'   => $request->input('icon_display_mode', 'icon_text'),
            'custom_text_color'   => $request->input('custom_text_color'),
            'custom_icon_color'   => $request->input('custom_icon_color'),
            'field_toggles'       => json_encode($fieldToggles),
            'is_active'           => 1,
            'created_at'          => now(),
            'updated_at'          => now()
        ]);

        return response()->json([
            'success'   => true,
            'message'   => 'Naya Digital Card view successfully create ho gaya!',
            'card_slug' => $cardSlug
        ]);
    }

    // 5. Pincode AJAX API (pincodes Table Fetch)
    public function getPincodeDetails(Request $request)
    {
        $pincode = trim($request->query('pincode'));

        $records = DB::table('pincodes')
            ->where('pincode', $pincode)
            ->select('office_name', 'district', 'state_name')
            ->get();

        if ($records->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Pincode nahi mila']);
        }

        return response()->json([
            'success' => true,
            'areas'   => $records->pluck('office_name')->unique()->values(),
            'city'    => $records->first()->district,
            'state'   => $records->first()->state_name
        ]);
    }

    // 6. Delete Card View
    public function destroyCardView($id)
    {
        DB::table('user_card_views')
            ->where('id', $id)
            ->where('user_id', auth()->id())
            ->delete();

        return redirect()->back()->with('success', 'Card View delete ho gaya!');
    }

    // 7. Public Show View
    public function show($slug)
    {
        $cardView = DB::table('user_card_views')->where('card_slug', $slug)->first();
        if (!$cardView) abort(404);

        $masterCard = DB::table('visiting_cards')->where('id', $cardView->visiting_card_id)->first();
        if (!$masterCard) abort(404);

        return view('vendor.card.show', compact('cardView', 'masterCard'));
    }
}