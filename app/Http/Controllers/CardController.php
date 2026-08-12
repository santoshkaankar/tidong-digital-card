<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VisitingCard;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class CardController extends Controller
{
    /**
     * Display a listing of the created cards.
     */
    public function index()
    {
        $cards = VisitingCard::all();
        return view('customer.visiting-cards-index', compact('cards'));
    }

    /**
     * Show the form for creating a new card.
     */
    public function create()
    {
        return view('customer.visiting-card-create');
    }

    /**
     * Store a newly created card in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'business_name' => 'required|string|max:255',
            'tagline' => 'nullable|string|max:255',
            'owner_name' => 'nullable|string|max:255',
            'phone' => 'required|string|max:20',
            'alt_phone' => 'nullable|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'gmail' => 'nullable|email|max:255',
            'yahoo_email' => 'nullable|email|max:255',
            'other_email' => 'nullable|email|max:255',
            'facebook' => 'nullable|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'twitter_x' => 'nullable|string|max:255',
            'linkedin' => 'nullable|string|max:255',
            'youtube' => 'nullable|string|max:255',
            'telegram' => 'nullable|string|max:255',
            'website_link' => 'nullable|string|max:255',
            'map_location_link' => 'nullable|string|max:255',
            'phonepe' => 'nullable|string|max:20',
            'gpay' => 'nullable|string|max:20',
            'paytm' => 'nullable|string|max:20',
            'upi_id' => 'nullable|string|max:255',
            'about_us' => 'nullable|string',
            'services_or_products' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'qr_code' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'address' => 'nullable|string|max:255',
            'area' => 'required|string|max:255',
            'pincode' => 'required|string|max:10',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
        ]);

        // Unique Card Number & User Tracking
        $validatedData['card_no'] = 'TDC-' . strtoupper(uniqid());
        $validatedData['user_id'] = auth()->id();

        if ($request->hasFile('photo')) {
            $validatedData['photo'] = $request->file('photo')->store('card_photos', 'public');
        }

        if ($request->hasFile('qr_code')) {
            $validatedData['qr_code'] = $request->file('qr_code')->store('card_qrs', 'public');
        }

        $card = VisitingCard::create($validatedData);

        return redirect()->route('customer.card.show', $card->id)
                         ->with('success', 'Digital Visiting Card generated successfully! 🚀');
    }

    /**
     * Show a single card view.
     */
    public function show($id)
    {
        $card = VisitingCard::findOrFail($id);
        return view('customer.visiting-card-view', compact('card'));
    }

    /**
     * Location Search API
     */
    public function searchLocations(Request $request)
    {
        $search = $request->get('q');

        $locations = DB::table('locations') 
            ->where('area', 'LIKE', "%{$search}%")
            ->orWhere('pincode', 'LIKE', "%{$search}%")
            ->orWhere('city', 'LIKE', "%{$search}%")
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => "{$item->area} - {$item->pincode} ({$item->city}, {$item->state})",
                    'area' => $item->area,
                    'pincode' => $item->pincode,
                    'city' => $item->city,
                    'state' => $item->state,
                ];
            });

        return response()->json($locations);
    }
}