<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Member\VisitingCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MemberManagementController extends Controller
{
    private $tidongCode = '12'; // Tidong Code (Fixed)

    public function index(Request $request)
    {
        $query = trim($request->input('query'));
        $member = null;
        $card = null;
        $searched = $request->filled('query');

        if ($searched) {
            $member = User::where('email', $query)
                        ->orWhere('mobile', $query)
                        ->orWhere('username', $query)
                        ->first();
                        
            if ($member) {
                $card = VisitingCard::where('user_id', $member->id)->first();
            }
        }

        return view('admin.members.manage', compact('member', 'card', 'query', 'searched'));
    }

    // Address & Location search (pincodes table se)
    public function searchLocations(Request $request)
    {
        $search = trim($request->get('q'));

        if (empty($search)) {
            return response()->json(['results' => []]);
        }

        $locations = DB::table('pincodes')
            ->where('office_name', 'LIKE', "%{$search}%")
            ->orWhere('pincode', 'LIKE', "%{$search}%")
            ->orWhere('district', 'LIKE', "%{$search}%")
            ->limit(20)
            ->get();

        $results = $locations->map(function ($item) {
            return [
                'id'       => $item->office_name,
                'text'     => $item->office_name . ' (' . $item->pincode . ', ' . $item->district . ')',
                'area'     => $item->office_name,
                'pincode'  => $item->pincode,
                'city'     => $item->district,
                'state'    => $item->state_name
            ];
        });

        return response()->json(['results' => $results]);
    }

    public function saveOrUpdate(Request $request)
    {
        if ($request->has('new_member_setup')) {
            $request->validate([
                'name'   => 'required|string|max:255',
                'email'  => 'required|email|unique:users,email',
                'mobile' => 'required|string|max:15',
                'state'  => 'nullable|string',
            ]);

            // Database se Dynamic Country Code aur State Code fetch karke Card Generate karna
            $generatedCardNo = $this->generateCardNumber(
                $request->state ?? 'Rajasthan', 
                $request->country ?? 'India'
            );

            $user = User::create([
                'name'     => $request->name,
                'email'    => trim($request->email),
                'mobile'   => $request->mobile,
                'role'     => 'member',
                'status'   => 'approved',
                'password' => Hash::make(Str::random(8)),
            ]);

            VisitingCard::create([
                'user_id'       => $user->id,
                'card_no'       => $generatedCardNo,
                'name'          => $user->name,
                'business_name' => $request->business_name ?? null,
                'gmail'         => $user->email,
                'phone'         => $user->mobile,
                'state'         => $request->state ?? 'Rajasthan',
            ]);

            return redirect()->route('admin.members.manage', ['query' => $user->email])
                             ->with('success', 'New member setup completed! Now configure the details.');
        }

        return redirect()->route('admin.members.manage');
    }

    public function updateDetails(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $card = VisitingCard::firstOrNew(['user_id' => $user->id]);

        if (empty($card->card_no)) {
            $card->card_no = $this->generateCardNumber(
                $request->state ?? 'Rajasthan', 
                $request->country ?? 'India'
            );
        }

        $inputData = $request->except(['_token', '_method', 'photo', 'qr_code', 'user_id']);

        foreach ($inputData as $key => $value) {
            $card->$key = ($value === '' || $value === null) ? null : $value;
        }

        if ($request->hasFile('photo')) {
            $card->photo = $request->file('photo')->store('cards/photos', 'public');
        }
        if ($request->hasFile('qr_code')) {
            $card->qr_code = $request->file('qr_code')->store('cards/qrs', 'public');
        }

        $card->user_id = $user->id;
        $card->save();

        if ($request->has('name')) {
            $user->update([
                'name'   => $request->name,
                'mobile' => $request->phone ?? $user->mobile,
            ]);
        }

        return redirect()->route('admin.members.manage', ['query' => $user->email])
                         ->with('success', 'Master profile configuration updated successfully!');
    }






    public function searchmember(Request $request)
{
    $query = VisitingCard::query();

    // 1. Keyword Filter (Name, Business Name, Phone, Email, Card No)
    if ($request->filled('search_name')) {
        $searchTerm = $request->search_name;
        $query->where(function ($q) use ($searchTerm) {
            $q->where('name', 'LIKE', "%{$searchTerm}%")
              ->orWhere('business_name', 'LIKE', "%{$searchTerm}%")
              ->orWhere('phone', 'LIKE', "%{$searchTerm}%")
              ->orWhere('gmail', 'LIKE', "%{$searchTerm}%")
              ->orWhere('card_no', 'LIKE', "%{$searchTerm}%");
        });
    }

    // 2. State Filter
    if ($request->filled('state')) {
        $query->where('state', $request->state);
    }

    // 3. City Filter
    if ($request->filled('city')) {
        $query->where('city', $request->city);
    }

    $results = $query->get();
    $hasSearched = $request->anyFilled(['search_name', 'state', 'city']);

    // 4. States Dropdown (Direct visiting_cards table se)
    $states = VisitingCard::whereNotNull('state')
                ->where('state', '!=', '')
                ->pluck('state')
                ->unique();

    // 5. Cities Dropdown (Direct visiting_cards table se)
    $citiesQuery = VisitingCard::whereNotNull('city')
                    ->where('city', '!=', '');

    if ($request->filled('state')) {
        $citiesQuery->where('state', $request->state);
    }
    $cities = $citiesQuery->pluck('city')->unique();

    return view('admin.members.search_member', compact('results', 'hasSearched', 'states', 'cities'));
}







    /**
     * Card Generation Logic
     * Format: {TidongCode}{CountryCode}-{StateCode}{Statewise_Serial_0000001}
     * Example: 12091-080000001
     */
    private function generateCardNumber($stateName = 'Rajasthan', $countryName = 'India')
    {
        // 1. Table se Country Code fetch karna (Default fallback: 091)
        $countryCode = DB::table('countries')
            ->where('name', $countryName)
            ->value('code') ?? '091';

        // 2. Table se State Code fetch karna (Default fallback: 08)
        $stateCode = DB::table('states')
            ->where('name', $stateName)
            ->value('code') ?? '08';

        // Formatting Codes
        $formattedCountryCode = str_pad($countryCode, 3, '0', STR_PAD_LEFT); // 3-digit e.g., 091
        $formattedStateCode   = str_pad($stateCode, 2, '0', STR_PAD_LEFT);   // 2-digit e.g., 08

        // Full Pattern Prefix: 12 + 091 + - + 08 => 12091-08
        $cardPrefix = $this->tidongCode . $formattedCountryCode . '-' . $formattedStateCode;

        // 3. State-wise serial number calculation (Har state ke liye 0000001 se alag start hoga)
        $lastCard = VisitingCard::where('card_no', 'LIKE', $cardPrefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastCard && preg_match('/-' . preg_quote($formattedStateCode, '/') . '(\d{7})$/', $lastCard->card_no, $matches)) {
            $nextSerial = intval($matches[1]) + 1;
        } else {
            $nextSerial = 1; // Naye state ke liye 1 se shuru hoga
        }

        $serialStr = str_pad($nextSerial, 7, '0', STR_PAD_LEFT); // 7-digit Serial e.g., 0000001

        return $cardPrefix . $serialStr; // Result: 12091-080000001
    }
}