<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\Member\VisitingCard;
use App\Models\User;
use Carbon\Carbon;

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
        $user = User::findOrFail(Auth::id());
        $card = VisitingCard::firstOrCreate(['user_id' => $user->id]);

        // 1. Form Validation Rules
        $request->validate([
            'dob'                  => 'nullable|date',
            'mobile'               => 'nullable|string|max:15',
            'pan_number'           => 'nullable|string|max:10',
            'aadhaar_number'       => 'nullable|string|max:12',
            'ifsc_code'            => 'nullable|string|max:11',
            'pan_image'            => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'aadhaar_front_image'  => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'aadhaar_back_image'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'profile_photo'        => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'banner_image'         => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // 2. Profile & Banner Photo Upload
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

        // 3. KYC Files Upload
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

        // 4. Update User Data
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

        // 5. Age Check Logic (Under 18 Block & Forced Logout)
        if ($request->filled('dob')) {
            $age = Carbon::parse($request->dob)->age;

            if ($age < 18) {
                // Block User Account
                if (\Schema::hasColumn('users', 'status')) {
                    $user->status = 'blocked';
                } elseif (\Schema::hasColumn('users', 'is_active')) {
                    $user->is_active = 0;
                }

                $user->save();
                $card->save();

                // Direct Logout
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->with('error', 'Your age is under 18 years. Your account has been blocked.');
            }
        }

        $user->save();
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