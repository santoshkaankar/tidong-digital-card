<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class VisitingCardController extends Controller
{
    public function index()
    {
        // Index logic
    }

    public function create()
    {
        return view('visiting-card-form');
    }

    public function store(Request $request)
    {
        // 1. Validation
        $request->validate([
            'name' => 'required|string|max:255',
            'business_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'area' => 'required|string|max:255',
            'pincode' => 'required|string|max:10',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'qr_code' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // 2. Photo Upload
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('photos', 'public');
        }

        // 3. QR Code Upload
        $qrPath = null;
        if ($request->hasFile('qr_code')) {
            $qrPath = $request->file('qr_code')->store('qrcodes', 'public');
        }

        // 4. Database Insert
        DB::table('visiting_cards')->insert([
            'name' => $request->name,
            'business_name' => $request->business_name,
            'tagline' => $request->tagline,
            'owner_name' => $request->owner_name,
            'phone' => $request->phone,
            'alt_phone' => $request->alt_phone,
            'whatsapp' => $request->whatsapp,
            'gmail' => $request->gmail,
            'yahoo_email' => $request->yahoo_email,
            'other_email' => $request->other_email,
            'facebook' => $request->facebook,
            'instagram' => $request->instagram,
            'twitter_x' => $request->twitter_x,
            'linkedin' => $request->linkedin,
            'youtube' => $request->youtube,
            'telegram' => $request->telegram,
            'website_link' => $request->website_link,
            'map_location_link' => $request->map_location_link,
            'phonepe' => $request->phonepe,
            'gpay' => $request->gpay,
            'paytm' => $request->paytm,
            'upi_id' => $request->upi_id,
            'about_us' => $request->about_us,
            'services_or_products' => $request->services_or_products,
            'address' => $request->address,
            'area' => $request->area,
            'pincode' => $request->pincode,
            'city' => $request->city,
            'state' => $request->state,
            'photo' => $photoPath,      
            'qr_code' => $qrPath,        
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Digital Visiting Card Successfully Save Ho Gaya!');
    }

    public function edit($id)
    {
        $card = DB::table('visiting_cards')->where('id', $id)->first();

        if (!$card) {
            abort(404);
        }

        return view('visiting-card-form', compact('card'));
    }

    public function update(Request $request, $id)
    {
        // 1. Validation
        $request->validate([
            'name' => 'required|string|max:255',
            'business_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'area' => 'required|string|max:255',
            'pincode' => 'required|string|max:10',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'qr_code' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $card = DB::table('visiting_cards')->where('id', $id)->first();
        if (!$card) {
            abort(404);
        }

        // 2. Photo Update / Null Handling
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

        // 3. QR Code Update / Null Handling
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

        // 4. Database Update
        DB::table('visiting_cards')->where('id', $id)->update([
            'name' => $request->name,
            'business_name' => $request->business_name,
            'tagline' => $request->tagline,
            'owner_name' => $request->owner_name,
            'phone' => $request->phone,
            'alt_phone' => $request->alt_phone,
            'whatsapp' => $request->whatsapp,
            'gmail' => $request->gmail,
            'yahoo_email' => $request->yahoo_email,
            'other_email' => $request->other_email,
            'facebook' => $request->facebook,
            'instagram' => $request->instagram,
            'twitter_x' => $request->twitter_x,
            'linkedin' => $request->linkedin,
            'youtube' => $request->youtube,
            'telegram' => $request->telegram,
            'website_link' => $request->website_link,
            'map_location_link' => $request->map_location_link,
            'phonepe' => $request->phonepe,
            'gpay' => $request->gpay,
            'paytm' => $request->paytm,
            'upi_id' => $request->upi_id,
            'about_us' => $request->about_us,
            'services_or_products' => $request->services_or_products,
            'address' => $request->address,
            'area' => $request->area,
            'pincode' => $request->pincode,
            'city' => $request->city,
            'state' => $request->state,
            'photo' => $photoPath,      
            'qr_code' => $qrPath,        
            'updated_at' => now(),
        ]);

        return redirect()->route('card.edit', $id)->with('success', 'Digital Visiting Card Successfully Update Ho Gaya!');
    }

    public function show($id)
    {
        $card = DB::table('visiting_cards')->where('id', $id)->first();
        if (!$card) { abort(404); }
        return view('card-show', compact('card'));
    }

    public function searchLocations(Request $request)
    {
        $search = $request->get('q');
        $locations = DB::table('pincodes')
                    ->where('pincode', 'LIKE', "%{$search}%")
                    ->orWhere('office_name', 'LIKE', "%{$search}%")
                    ->orWhere('district', 'LIKE', "%{$search}%")
                    ->orWhere('state_name', 'LIKE', "%{$search}%")
                    ->limit(30)
                    ->get();

        $formatted = [];
        foreach ($locations as $loc) {
            $formatted[] = [
                'id' => $loc->pincode,
                'text' => $loc->office_name . ' - ' . $loc->pincode . ' (' . $loc->district . ', ' . $loc->state_name . ')',
                'area' => $loc->office_name,      
                'pincode' => $loc->pincode,
                'city' => $loc->district,        
                'state' => $loc->state_name      
            ];
        }
        return response()->json($formatted);
    }
}