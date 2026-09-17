<?php

namespace App\Http\Controllers\Member;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Member\VisitingCard;
use App\Models\Member\UserCardView;
use App\Models\Country;
use App\Models\State;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CardController extends Controller
{
    public function index()
    {
        $masterCard = VisitingCard::where('user_id', Auth::id())->first();
        
        if (!$masterCard) {
            return redirect()->route('member.card.configure')
                ->with('info', 'Pahle apni Master Profile details complete karein.');
        }

        $cardViews = UserCardView::where('user_id', Auth::id())->latest()->get();

        return view('member.card.cards-index', compact('masterCard', 'cardViews'));
    }

    public function configure(Request $request)
    {
        $countries = Country::all();
        $states = State::all();
        $card = VisitingCard::where('user_id', Auth::id())->first();

        return view('member.card.configure', compact('countries', 'states', 'card'));
    }

    public function create()
    {
        $masterCard = VisitingCard::where('user_id', Auth::id())->first();

        if (!$masterCard) {
            return redirect()->route('member.card.configure')
                ->with('info', 'Pahle apni Master Profile details complete karein.');
        }

        return view('member.card.create_design', compact('masterCard'));
    }

    public function storeMaster(Request $request)
    {
        $request->validate([
            'name'                 => 'required|string|max:255',
            'designation'          => 'nullable|string|max:255',
            'business_name'        => 'nullable|string|max:255',
            'tagline'              => 'nullable|string|max:255',
            'owner_name'           => 'nullable|string|max:255',
            'nickname'             => 'nullable|string|max:255',
            'phone'                => 'required|string|max:20',
            'alt_phone'            => 'nullable|string|max:20',
            'whatsapp'             => 'nullable|string|max:20',
            'gmail'                => 'nullable|email|max:255',
            'yahoo_email'          => 'nullable|email|max:255',
            'other_email'          => 'nullable|email|max:255',
            'facebook'             => 'nullable|string|max:255',
            'instagram'            => 'nullable|string|max:255',
            'linkedin'             => 'nullable|string|max:255',
            'youtube'              => 'nullable|string|max:255',
            'telegram'             => 'nullable|string|max:255',
            'website'              => 'nullable|string|max:255',
            'location_url'         => 'nullable|string|max:500',
            'gpay'                 => 'nullable|string|max:50',
            'paytm'                => 'nullable|string|max:50',
            'upi_id'               => 'nullable|string|max:255',
            'address'              => 'nullable|string|max:500',
            'area'                 => 'nullable|string|max:255',
            'pincode'              => 'nullable|string|max:20',
            'city'                 => 'nullable|string|max:100',
            'state'                => 'nullable|string|max:100',
            'about_us'             => 'nullable|string',
            'services_or_products' => 'nullable|string',
            'other_details'        => 'nullable|string',
            'country_id'           => 'nullable|exists:countries,id',
            'state_id'             => 'nullable|exists:states,id',
            'photo'                => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'qr_code'              => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'font_family'          => 'nullable|string|max:255',
            'icon_style'           => 'nullable|string|max:255',
            'icon_color'           => 'nullable|string|max:50',
            'text_color'           => 'nullable|string|max:50',
        ]);

        $card = VisitingCard::firstOrNew(['user_id' => Auth::id()]);

        // Fill form attributes first
        $card->fill($request->except(['_token', 'photo', 'qr_code', 'card_no']));

        // Generate Card Number if creating a new master card
        if (!$card->exists) {
            $tidongId = '12';
            $countryObj = $request->input('country_id') ? Country::find($request->input('country_id')) : null;
            $countryCode = $countryObj ? $countryObj->code : '091';

            $stateObj = $request->input('state_id') ? State::find($request->input('state_id')) : null;
            $stateCode = $stateObj ? $stateObj->code : '08';
            $stateName = $stateObj ? $stateObj->name : ($request->input('state') ?: 'Rajasthan');

            $stateCardCount = VisitingCard::whereRaw('LOWER(state) LIKE ?', ['%' . strtolower($stateName) . '%'])->count() + 1;
            $serialNo = str_pad($stateCardCount, 7, '0', STR_PAD_LEFT);

            $card->card_no   = "{$tidongId}{$countryCode}-{$stateCode}{$serialNo}";
            $card->state     = $stateName;
            $card->plan_type = 'free';
        }

        // Photo Upload Logic
        if ($request->hasFile('photo')) {
            $this->deleteStoredFile($card->photo);
            $path = $request->file('photo')->store('uploads/photos', 'public');
            $card->photo = 'storage/' . $path;
        }

        // QR Code Upload Logic
        if ($request->hasFile('qr_code')) {
            $this->deleteStoredFile($card->qr_code);
            $path = $request->file('qr_code')->store('uploads/qrcodes', 'public');
            $card->qr_code = 'storage/' . $path;
        }

        $card->save();

        return redirect()->route('member.card.configure')
            ->with('success', 'Master profile successfully save ho gayi!');
    }

    /**
     * Helper to clean up existing uploaded file from public disk
     */
    private function deleteStoredFile(?string $filePath): void
    {
        if (!empty($filePath)) {
            $relativePath = str_replace('storage/', '', $filePath);
            if (Storage::disk('public')->exists($relativePath)) {
                Storage::disk('public')->delete($relativePath);
            }
        }
    }

    public function store(Request $request)
    {
        return $this->storeMaster($request);
    }

    public function update(Request $request, $id = null)
    {
        return $this->storeMaster($request);
    }

    public function updateProfile(Request $request)
    {
        return $this->storeMaster($request);
    }

    public function createDesign()
    {
        return $this->create();
    }

    public function storeDesign(Request $request)
    {
        $request->validate([
            'theme_style'        => 'required|string',
            'font_family'        => 'nullable|string',
            'icon_style'         => 'nullable|string',
            'icon_display_mode'  => 'nullable|string',
            'custom_text_color'  => 'nullable|string',
            'custom_icon_color'  => 'nullable|string',
            'custom_bg_color'    => 'nullable|string',
            'text_color'         => 'nullable|string',
            'icon_color'         => 'nullable|string',
            'bg_color'           => 'nullable|string',
            'toggles'            => 'nullable|array'
        ]);

        $masterCard = VisitingCard::where('user_id', Auth::id())->firstOrFail();

        $themeCategoryMap = [
            'default'          => 'A',
            'classic-modern'   => 'A',
            'classic-dark'     => 'A',
            'classic-white'    => 'A',
            'fabric-cotton'    => 'B',
            'fabric-denim'     => 'B',
            'fabric-silk'      => 'B',
            'fabric-canvas'    => 'B',
            'fabric-velvet'    => 'B',
            'stone-marble'     => 'C',
            'stone-granite'    => 'C',
            'stone-slate'      => 'C',
            'wood-oak'         => 'D',
            'wood-walnut'      => 'D',
            'wood-teak'        => 'D',
            'metal-gold'       => 'E',
            'metal-silver'     => 'E',
            'metal-bronze'     => 'E',
            'leather-black'    => 'F',
            'vintage-paper'    => 'F',
            'paper-parchment'  => 'F',
            'crystal-glass'    => 'G',
            'dark-obsidian'    => 'G',
        ];

        $selectedTheme = $request->input('theme_style', 'default');
        $categoryCode  = $themeCategoryMap[$selectedTheme] ?? 'Z';

        $maxVariant = UserCardView::where('user_id', Auth::id())->max('variant_number') ?? 0;
        $variantNo  = $maxVariant + 1;

        do {
            $designVariantCode = $categoryCode . $variantNo;
            $fullCardNo        = $masterCard->card_no . '-' . $designVariantCode;
            
            $exists = UserCardView::where('full_card_no', $fullCardNo)->exists();
            if ($exists) {
                $variantNo++;
            }
        } while ($exists);

        // Standardized toggles map with default fallbacks
        $defaultToggles = [
            'show_name'          => true,
            'show_photo'         => false,
            'show_business_name' => true,
            'show_designation'   => true,
            'show_tagline'       => false,
            'show_nickname'      => false,
            'show_phone'         => true,
            'show_alt_phone'     => false,
            'show_whatsapp'      => true,
            'show_gmail'         => false,
            'show_yahoo_email'   => false,
            'show_other_email'   => false,
            'show_website'       => false,
            'show_facebook'      => false,
            'show_instagram'     => false,
            'show_linkedin'      => false,
            'show_youtube'       => false,
            'show_telegram'      => false,
            'show_upi_id'        => false,
            'show_gpay'          => false,
            'show_paytm'         => false,
            'show_qr_code'       => false,
            'show_about'         => false,
            'show_other_details' => false,
            'show_address'       => false,
            'show_area'          => false,
            'show_pincode'       => false,
            'show_city'          => false,
            'show_state'         => false,
            'show_location_url'  => false,
        ];

        $rawToggles = $request->input('toggles', []);
        $toggles = [];
        foreach ($defaultToggles as $key => $defaultValue) {
            $toggles[$key] = isset($rawToggles[$key]) ? (bool)$rawToggles[$key] : $defaultValue;
        }

        // Color Fallbacks
        $textColor = $request->input('custom_text_color') ?: ($request->input('text_color') ?: '#ffffff');
        $iconColor = $request->input('custom_icon_color') ?: ($request->input('icon_color') ?: '#ffffff');
        $bgColor   = $request->input('custom_bg_color')   ?: ($request->input('bg_color')   ?: '#1e293b');

        UserCardView::create([
            'user_id'             => Auth::id(),
            'visiting_card_id'    => $masterCard->id,
            'card_slug'           => Str::random(8),
            'theme_style'         => $selectedTheme,
            'theme_category_code' => $categoryCode,
            'variant_number'      => $variantNo,
            'full_card_no'        => $fullCardNo,
            'font_family'         => $request->input('font_family'),
            'icon_style'          => $request->input('icon_style', 'regular'),
            'icon_display_mode'   => $request->input('icon_display_mode', 'icon_only'),
            'custom_text_color'   => $textColor,
            'custom_icon_color'   => $iconColor,
            'custom_bg_color'     => $bgColor,
            'field_toggles'       => $toggles,
            'is_active'           => true,
        ]);

        return redirect()->route('member.card.index')
            ->with('success', "Naya card variant ($fullCardNo) safaltapurvak create ho gaya hai!");
    }

    public function showPublic($slug)
    {
        $cardView = UserCardView::where('card_slug', $slug)->firstOrFail();
        $masterCard = VisitingCard::findOrFail($cardView->visiting_card_id);

        return view('member.card.show', compact('cardView', 'masterCard'));
    }

    public function destroyView($id)
    {
        $view = UserCardView::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $view->delete();

        return redirect()->route('member.card.index')
            ->with('success', 'Card variant successfully delete ho gaya!');
    }

    public function destroy($id)
    {
        return $this->destroyView($id);
    }

    public function searchLocations(Request $request)
    {
        $search = trim($request->get('q'));
        
        if (empty($search)) {
            return response()->json([]);
        }

        $locations = DB::table('pincodes')
            ->whereRaw('LOWER(office_name) LIKE ?', ['%' . strtolower($search) . '%'])
            ->orWhere('pincode', 'LIKE', "%{$search}%")
            ->orWhereRaw('LOWER(district) LIKE ?', ['%' . strtolower($search) . '%'])
            ->orWhereRaw('LOWER(state_name) LIKE ?', ['%' . strtolower($search) . '%'])
            ->limit(30)
            ->get();

        $formattedData = [];
        foreach ($locations as $loc) {
            $formattedData[] = [
                'id'      => $loc->pincode,
                'text'    => $loc->office_name . ' - ' . ($loc->pincode ?? '') . ' (' . $loc->district . ', ' . $loc->state_name . ')',
                'area'    => $loc->office_name,
                'pincode' => $loc->pincode,
                'city'    => $loc->district,
                'state'   => $loc->state_name
            ];
        }

        return response()->json($formattedData);
    }
}