<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VisitingCard;
use Illuminate\Support\Facades\Auth;

class CardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cards = VisitingCard::where('user_id', Auth::id())->get();
        return view('member.card.index', compact('cards'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('member.card.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'business_name' => 'nullable|string|max:255',
        ]);

        $validated['user_id'] = Auth::id();
        $card = VisitingCard::create($validated);

        return redirect()->route('member.card.show', $card->id);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $card = VisitingCard::findOrFail($id);
        return view('member.card.show', compact('card'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $card = VisitingCard::findOrFail($id);
        return view('member.card.edit', compact('card'));
    }

    /**
     * Show the customize/display settings view.
     */
    public function customize($id)
    {
        $card = VisitingCard::findOrFail($id);
        return view('member.card.customize', compact('card'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $card = VisitingCard::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'business_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'gmail' => 'nullable|email|max:255',
            'facebook' => 'nullable|url|max:255',
            'instagram' => 'nullable|url|max:255',
            'website_link' => 'nullable|url|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
        ]);

        $card->update($validated);

        return redirect()->route('member.card.show', $card->id)
                         ->with('success', 'Visiting card updated successfully!');
    }

    /**
     * Update the display toggles/preferences for the card.
     */
    public function updateDisplay(Request $request, $id)
    {
        $card = VisitingCard::findOrFail($id);
        
        $card->update([
            'show_business' => $request->has('show_business') ? 1 : 0,
            'show_tagline' => $request->has('show_tagline') ? 1 : 0,
            'show_phone' => $request->has('show_phone') ? 1 : 0,
            'show_alt_phone' => $request->has('show_alt_phone') ? 1 : 0,
            'show_whatsapp' => $request->has('show_whatsapp') ? 1 : 0,
            'show_gmail' => $request->has('show_gmail') ? 1 : 0,
            'show_yahoo_email' => $request->has('show_yahoo_email') ? 1 : 0,
            'show_other_email' => $request->has('show_other_email') ? 1 : 0,
            'show_facebook' => $request->has('show_facebook') ? 1 : 0,
            'show_instagram' => $request->has('show_instagram') ? 1 : 0,
            'show_twitter_x' => $request->has('show_twitter_x') ? 1 : 0,
            'show_linkedin' => $request->has('show_linkedin') ? 1 : 0,
            'show_youtube' => $request->has('show_youtube') ? 1 : 0,
            'show_telegram' => $request->has('show_telegram') ? 1 : 0,
            'show_website' => $request->has('show_website') ? 1 : 0,
            'show_phonepe' => $request->has('show_phonepe') ? 1 : 0,
            'show_gpay' => $request->has('show_gpay') ? 1 : 0,
            'show_paytm' => $request->has('show_paytm') ? 1 : 0,
            'show_upi' => $request->has('show_upi') ? 1 : 0,
            'show_about_us' => $request->has('show_about_us') ? 1 : 0,
            'show_services' => $request->has('show_services') ? 1 : 0,
            'show_photo' => $request->has('show_photo') ? 1 : 0,
            'show_qr_code' => $request->has('show_qr_code') ? 1 : 0,
            'show_address' => $request->has('show_address') ? 1 : 0,
            'show_map' => $request->has('show_map') ? 1 : 0,
        ]);

        return redirect()->route('member.card.show', $card->id)
                         ->with('success', 'Card display preferences updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $card = VisitingCard::findOrFail($id);
        $card->delete();

        return redirect()->route('member.cards.index')
                         ->with('success', 'Visiting card deleted successfully!');
    }
}