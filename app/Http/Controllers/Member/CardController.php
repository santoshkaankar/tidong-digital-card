<?php

namespace App\Http\Controllers\Member;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Member\VisitingCard;
use App\Models\Member\UserCardView;
use App\Models\Member\Country;
use App\Models\Member\State;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CardController extends Controller
{
    public function index()
    {
        $masterCard = VisitingCard::where('user_id', Auth::id())->first();
        
        if (!$masterCard) {
            return redirect()->route('card.configure')
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
            return redirect()->route('card.configure')
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
        ]);

        $card = VisitingCard::firstOrNew(['user_id' => Auth::id()]);

        if (!$card->exists) {
            $tidongId = '12';
            $countryObj = Country::find($request->input('country_id'));
            $countryCode = $countryObj ? $countryObj->code : '091';

            $stateObj = State::find($request->input('state_id'));
            $stateCode = $stateObj ? $stateObj->code : '08';
            $stateName = $stateObj ? $stateObj->name : 'Rajasthan';

            $stateCardCount = VisitingCard::where('state', 'LIKE', "%{$stateName}%")->count() + 1;
            $serialNo = str_pad($stateCardCount, 7, '0', STR_PAD_LEFT);

            $card->card_no = "{$tidongId}{$countryCode}-{$stateCode}{$serialNo}";
            $card->state   = $stateName;
            $card->plan_type = 'free';
        }

        if ($request->hasFile('photo')) {
            if (!empty($card->photo) && Storage::disk('public')->exists(str_replace('storage/', '', $card->photo))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $card->photo));
            }

            $file = $request->file('photo');
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('uploads/photos', $filename, 'public');
            $card->photo = 'storage/' . $path;
        }

        if ($request->hasFile('qr_code')) {
            if (!empty($card->qr_code) && Storage::disk('public')->exists(str_replace('storage/', '', $card->qr_code))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $card->qr_code));
            }

            $file = $request->file('qr_code');
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('uploads/qrcodes', $filename, 'public');
            $card->qr_code = 'storage/' . $path;
        }

        $card->fill($request->except(['_token', 'photo', 'qr_code']));
        $card->save();

        return redirect()->route('card.configure')
            ->with('success', 'Master profile successfully save ho gayi!');
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
            'theme_style' => 'required|string',
            'toggles'     => 'nullable|array'
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

        $existingCount = UserCardView::where('user_id', Auth::id())->count();
        $variantNo = $existingCount + 1;
        
        $designVariantCode = $categoryCode . $variantNo;
        $fullCardNo = $masterCard->card_no . '-' . $designVariantCode;

        $toggles = [
            'show_name'          => true,
            'show_photo'         => $request->has('toggles.show_photo'),
            'show_business_name' => $request->has('toggles.show_business_name'),
            'show_designation'   => $request->has('toggles.show_designation'),
            'show_tagline'       => $request->has('toggles.show_tagline'),
            'show_nickname'      => $request->has('toggles.show_nickname'),
            'show_phone'         => $request->has('toggles.show_phone'),
            'show_alt_phone'     => $request->has('toggles.show_alt_phone'),
            'show_whatsapp'      => $request->has('toggles.show_whatsapp'),
            'show_gmail'         => $request->has('toggles.show_gmail'),
            'show_yahoo_email'   => $request->has('toggles.show_yahoo_email'),
            'show_other_email'   => $request->has('toggles.show_other_email'),
            'show_website'       => $request->has('toggles.show_website'),
            'show_facebook'      => $request->has('toggles.show_facebook'),
            'show_instagram'     => $request->has('toggles.show_instagram'),
            'show_linkedin'      => $request->has('toggles.show_linkedin'),
            'show_youtube'       => $request->has('toggles.show_youtube'),
            'show_telegram'      => $request->has('toggles.show_telegram'),
            'show_upi_id'        => $request->has('toggles.show_upi_id'),
            'show_gpay'          => $request->has('toggles.show_gpay'),
            'show_paytm'         => $request->has('toggles.show_paytm'),
            'show_qr_code'       => $request->has('toggles.show_qr_code'),
            'show_about'         => $request->has('toggles.show_about'),
            'show_other_details' => $request->has('toggles.show_other_details'),
            'show_address'       => $request->has('toggles.show_address'),
            'show_area'          => $request->has('toggles.show_area'),
            'show_pincode'       => $request->has('toggles.show_pincode'),
            'show_city'          => $request->has('toggles.show_city'),
            'show_state'         => $request->has('toggles.show_state'),
            'show_location_url'  => $request->has('toggles.show_location_url'),
        ];

        UserCardView::create([
            'user_id'             => Auth::id(),
            'visiting_card_id'    => $masterCard->id,
            'card_slug'           => Str::random(8),
            'theme_style'         => $selectedTheme,
            'theme_category_code' => $categoryCode,
            'variant_number'      => $variantNo,
            'full_card_no'        => $fullCardNo,
            'field_toggles'       => json_encode($toggles),
            'is_active'           => true,
        ]);

        return redirect()->route('cards.index')
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

        return redirect()->route('cards.index')
            ->with('success', 'Card variant successfully delete ho gaya!');
    }

    public function destroy($id)
    {
        return $this->destroyView($id);
    }

    public function searchLocations(Request $request)
    {
        $search = $request->get('q');
        
        $locations = DB::table('pincodes')
            ->where('office_name', 'LIKE', "%{$search}%")
            ->orWhere('pincode', 'LIKE', "%{$search}%")
            ->orWhere('district', 'LIKE', "%{$search}%")
            ->orWhere('state_name', 'LIKE', "%{$search}%")
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