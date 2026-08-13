<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VisitingCard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CardController extends Controller
{
    public function index()
    {
        $cards = VisitingCard::where('user_id', Auth::id())->get();
        return view('member.card.cards-index', compact('cards'));
    }

    public function create(Request $request)
    {
        // Defaulting to visiting-card so 'Card Detail' opens the correct input form
        $type = $request->get('type', 'visiting-card');
        $viewName = "member.card.{$type}-create";
        
        if (view()->exists($viewName)) {
            return view($viewName);
        }
        
        return view('member.card.visiting-card-create');
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
            'state' => 'nullable|string|max:100',
            'design_type' => 'nullable|string|max:50',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'qr_code' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Photo Upload
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('photos', 'public');
        }

        // QR Code Upload
        $qrPath = null;
        if ($request->hasFile('qr_code')) {
            $qrPath = $request->file('qr_code')->store('qrcodes', 'public');
        }

        $designType = $request->input('design_type', 'visiting-card');

        $data = $request->all();
        $data['user_id'] = Auth::id();
        $data['plan_type'] = 'free';
        $data['card_type'] = $designType;
        $data['card_no'] = 'TDC-' . strtoupper(Str::random(6));
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
                         ->with('success', 'Digital Visiting Card successfully create ho gaya!');
    }

    public function show($id)
    {
        $card = VisitingCard::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $type = $card->card_type ?? $card->design_type ?? 'visiting-card';

        $viewName = "member.card.{$type}-view";
        if (view()->exists($viewName)) {
            return view($viewName, compact('card'));
        }

        return view('member.card.visiting-card-view', compact('card'));
    }

    public function edit($id)
    {
        $card = VisitingCard::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $type = $card->card_type ?? $card->design_type ?? 'visiting-card';
        
        $viewName = "member.card.{$type}-create";
        if (view()->exists($viewName)) {
            return view($viewName, compact('card'));
        }

        return view('member.card.visiting-card-create', compact('card'));
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

    public function updateDisplay(Request $request, $id)
    {
        $card = VisitingCard::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        
        $card->update([
            'show_business' => $request->has('show_business') ? 1 : 0,
            'show_phone' => $request->has('show_phone') ? 1 : 0,
            'show_whatsapp' => $request->has('show_whatsapp') ? 1 : 0,
            'show_gmail' => $request->has('show_gmail') ? 1 : 0,
            'show_facebook' => $request->has('show_facebook') ? 1 : 0,
            'show_instagram' => $request->has('show_instagram') ? 1 : 0,
            'show_website' => $request->has('show_website') ? 1 : 0,
            'show_address' => $request->has('show_address') ? 1 : 0,
        ]);

        return redirect()->route('member.cards.index')
                         ->with('success', 'Card preferences updated successfully!');
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
                'text' => $loc->office_name . ' - ' . ($loc->pincode ?? '') . ' (' . $loc->district . ', ' . $loc->state_name . ')',
                'area' => $loc->office_name,
                'pincode' => $loc->pincode,
                'city' => $loc->district,
                'state' => $loc->state_name
            ];
        }

        return response()->json($formattedData);
    }
}