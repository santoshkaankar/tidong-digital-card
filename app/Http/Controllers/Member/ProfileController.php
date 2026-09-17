<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\Member\VisitingCard;
use App\Models\User;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        $card = VisitingCard::where('user_id', $user->id)->first();
        
        $wallet = DB::table('wallets')->where('user_id', $user->id)->first();
        $walletBalance = $wallet ? $wallet->real_balance : 0.00;
        $activePlan = null;
        
        $profileViews = $card && isset($card->views) ? $card->views : 1245;
        $cardShares = $card && isset($card->shares) ? $card->shares : 348;

        return view('member.profile', compact('user', 'card', 'walletBalance', 'activePlan', 'profileViews', 'cardShares'));
    }

    public function edit()
    {
        $user = Auth::user();
        $card = VisitingCard::where('user_id', $user->id)->first();
        return view('member.card.configure', compact('user', 'card'));
    }

    public function update(Request $request)
    {
        $user = User::find(Auth::id());
        $card = VisitingCard::where('user_id', $user->id)->first();

        if (!$card) {
            $card = new VisitingCard();
            $card->user_id = $user->id;
        }

        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            $photoPath = $request->file('profile_photo')->store('profiles', 'public');
            $user->profile_photo = $photoPath;
            $card->profile_photo = $photoPath;
        }

        if ($request->hasFile('banner_image')) {
            if ($card->banner_image && Storage::disk('public')->exists($card->banner_image)) {
                Storage::disk('public')->delete($card->banner_image);
            }
            $card->banner_image = $request->file('banner_image')->store('banners', 'public');
        }

        $kycUploaded = false;
        if ($request->hasFile('pan_image')) {
            $user->pan_image = $request->file('pan_image')->store('kyc', 'public');
            $kycUploaded = true;
        }
        if ($request->hasFile('aadhaar_front_image')) {
            $user->aadhaar_front_image = $request->file('aadhaar_front_image')->store('kyc', 'public');
            $kycUploaded = true;
        }
        if ($request->hasFile('aadhaar_back_image')) {
            $user->aadhaar_back_image = $request->file('aadhaar_back_image')->store('kyc', 'public');
            $kycUploaded = true;
        }

        if ($kycUploaded) {
            $user->kyc_status = 'pending';
        }

        $userFields = [
            'name', 'mobile', 'gender', 'dob', 'pan_number', 'aadhaar_number',
            'account_holder_name', 'bank_name', 'account_number', 'ifsc_code', 'upi_id',
            'address', 'area', 'city', 'state', 'pincode'
        ];

        foreach ($userFields as $field) {
            if ($request->has($field)) {
                if (in_array($field, ['pan_number', 'ifsc_code'])) {
                    $user->$field = strtoupper($request->input($field));
                } else {
                    $user->$field = $request->input($field);
                }
            }
        }
        $user->save();

        $card->fill($request->except(['profile_photo', 'banner_image']));
        $card->save();

        return redirect()->back()->with('success', 'Profile, KYC and Address updated successfully!');
    }

    /**
     * Live AJAX Search for Pincodes / City / Area / State
     */
    public function searchPincode(Request $request)
    {
        $query = trim($request->get('q', ''));
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $results = DB::table('pincodes')
            ->where('pincode', 'LIKE', "{$query}%")
            ->orWhere('office_name', 'LIKE', "%{$query}%")
            ->orWhere('district', 'LIKE', "%{$query}%")
            ->orWhere('state_name', 'LIKE', "%{$query}%")
            ->select('pincode', 'office_name as area', 'district as city', 'state_name as state')
            ->limit(15)
            ->get();

        return response()->json($results);
    }
}