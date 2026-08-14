<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VisitingCard;
use App\Models\Country;
use App\Models\State;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CardController extends Controller
{
    public function index()
    {
        $cards = VisitingCard::where('user_id', Auth::id())->get();
        return view('member.card.cards-index', compact('cards'));
    }

    // 1. Card Detail Form (Data save/feeding ke liye)
    public function configure(Request $request)
    {
        $countries = Country::all();
        $states = State::all();
        
        // Check karein ki is user ka pehle se card hai ya nahi
        $card = VisitingCard::where('user_id', Auth::id())->first();
        
        // Seedha configure view ko pass kar rahe hain data ke sath
        return view('member.card.configure', compact('countries', 'states', 'card'));
    }

    
    // 2. Create Card & Toggles Display ke liye
    public function create(Request $request)
    {
        $type = $request->get('type', 'modern');
        $countries = Country::all();
        $states = State::all();
        
        $viewName = "member.card.{$type}-create";
        if (view()->exists($viewName)) {
            return view($viewName, compact('countries', 'states'));
        }
        
        return view('member.card.modern-create', compact('countries', 'states'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'business_name' => 'nullable|string|max:255',
            'phone' => 'required|string|max:20',
            'alt_phone' => 'nullable|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'gmail' => 'nullable|email|max:255',
            'city' => 'nullable|string|max:100',
            'country_id' => 'nullable|exists:countries,id',
            'state_id' => 'nullable|exists:states,id',
            'design_type' => 'nullable|string|max:50',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'qr_code' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('photos', 'public');
        }

        $qrPath = null;
        if ($request->hasFile('qr_code')) {
            $qrPath = $request->file('qr_code')->store('qrcodes', 'public');
        }

        // --- Unique Card Number Generation Formula via Database ---
        $tidongId = '12';

        $countryObj = Country::find($request->input('country_id'));
        $countryCode = $countryObj ? $countryObj->code : '091';

        $stateObj = State::find($request->input('state_id'));
        $stateCode = $stateObj ? $stateObj->code : '08';
        $stateName = $stateObj ? $stateObj->name : 'Rajasthan';

        $stateCardCount = VisitingCard::where('state', 'LIKE', "%{$stateName}%")->count() + 1;
        $serialNo = str_pad($stateCardCount, 7, '0', STR_PAD_LEFT);

        $generatedCardNo = "{$tidongId}{$countryCode}-{$stateCode}{$serialNo}";
        // ----------------------------------------------------------

        $designType = $request->input('design_type', 'modern');

        $data = $request->all();
        $data['user_id'] = Auth::id();
        $data['plan_type'] = 'free';
        $data['card_type'] = $designType;
        $data['design_type'] = $designType;
        $data['card_no'] = $generatedCardNo;
        $data['state'] = $stateName;
        $data['photo'] = $photoPath;
        $data['qr_code'] = $qrPath;

        // Visibility Toggles
        $data['show_business'] = $request->has('show_business') ? 1 : 0;
        $data['show_phone'] = $request->has('show_phone') ? 1 : 0;
        $data['show_whatsapp'] = $request->has('show_whatsapp') ? 1 : 0;
        $data['show_gmail'] = $request->has('show_gmail') ? 1 : 0;
        $data['show_facebook'] = $request->has('show_facebook') ? 1 : 0;
        $data['show_instagram'] = $request->has('show_instagram') ? 1 : 0;
        $data['show_website'] = $request->has('show_website') ? 1 : 0;
        $data['show_address'] = $request->has('show_address') ? 1 : 0;

        VisitingCard::create($data);

        return redirect()->route('member.cards.index')
                         ->with('success', 'Digital Visiting Card successfully save ho gaya!');
    }

    public function show($id)
    {
        $card = VisitingCard::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $type = strtolower(trim($card->card_type ?? $card->design_type ?? 'modern'));
        $type = str_replace(['_', ' '], '-', $type);

        $viewName = "member.card.{$type}-view";
        if (view()->exists($viewName)) {
            return view($viewName, compact('card'));
        }

        return view('member.card.modern-view', compact('card'));
    }

    public function edit($id)
    {
        $card = VisitingCard::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $countries = Country::all();
        $states = State::all();

        $type = strtolower(trim($card->card_type ?? $card->design_type ?? 'modern'));
        $type = str_replace(['_', ' '], '-', $type);
        
        $viewName = "member.card.{$type}-create";
        if (view()->exists($viewName)) {
            return view($viewName, compact('card', 'countries', 'states'));
        }

        return view('member.card.modern-create', compact('card', 'countries', 'states'));
    }

    public function update(Request $request, $id)
    {
        $card = VisitingCard::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'business_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'qr_code' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $photoPath = $card->photo;
        if ($request->has('remove_photo')) {
            if ($card->photo && Storage::disk('public')->exists($card->photo)) {
                Storage::disk('public')->delete($card->photo);
            }
            $photoPath = null;
        } elseif ($request->hasFile('photo')) {
            if ($card->photo && Storage::disk('public')->exists($card->photo)) {
                Storage::disk('public')->delete($card->photo);
            }
            $photoPath = $request->file('photo')->store('photos', 'public');
        }

        $qrPath = $card->qr_code;
        if ($request->has('remove_qr')) {
            if ($card->qr_code && Storage::disk('public')->exists($card->qr_code)) {
                Storage::disk('public')->delete($card->qr_code);
            }
            $qrPath = null;
        } elseif ($request->hasFile('qr_code')) {
            if ($card->qr_code && Storage::disk('public')->exists($card->qr_code)) {
                Storage::disk('public')->delete($card->qr_code);
            }
            $qrPath = $request->file('qr_code')->store('qrcodes', 'public');
        }

        $data = $request->all();
        $data['photo'] = $photoPath;
        $data['qr_code'] = $qrPath;

        $card->update($data);

        return redirect()->route('member.cards.index')
                         ->with('success', 'Visiting card successfully update ho gaya!');
    }

    public function destroy($id)
    {
        $card = VisitingCard::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        
        if ($card->photo && Storage::disk('public')->exists($card->photo)) {
            Storage::disk('public')->delete($card->photo);
        }
        if ($card->qr_code && Storage::disk('public')->exists($card->qr_code)) {
            Storage::disk('public')->delete($card->qr_code);
        }

        $card->delete();

        return redirect()->route('member.cards.index')
                         ->with('success', 'Visiting card successfully delete ho gaya!');
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
                'id' => $loc->pincode,
                'hitext' => $loc->office_name . ' - ' . ($loc->pincode ?? '') . ' (' . $loc->district . ', ' . $loc->state_name . ')',
                'area' => $loc->office_name,
                'pincode' => $loc->pincode,
                'city' => $loc->district,
                'state' => $loc->state_name
            ];
        }

        return response()->json($formattedData);
    }
}
