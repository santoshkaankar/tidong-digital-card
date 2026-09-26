<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DeliveryProfileController extends Controller
{
    public function toggleDuty(Request $request)
    {
        $user = auth()->user();
        $user->is_online = !$user->is_online;
        $user->save();

        return back()->with('success', 'Duty status updated successfully.');
    }

    public function index()
    {
        $user = auth()->user();
        return view('delivery.profile.index', compact('user'));
    }

    // 1. Pincode Enter karne par City, State aur Areas List Fetch karna
    public function lookupPincode($pincode)
    {
        $records = DB::table('pincodes')->where('pincode', $pincode)->get();

        if ($records->isNotEmpty()) {
            $first = $records->first();

            $areas = $records->pluck('office_name')
                ->filter()
                ->unique()
                ->values()
                ->toArray();

            if (empty($areas)) {
                $areas = $records->pluck('circle_name')
                    ->filter()
                    ->unique()
                    ->values()
                    ->toArray();
            }

            return response()->json([
                'success'  => true,
                'district' => $first->district ?? '',
                'state'    => $first->state_name ?? '',
                'areas'    => $areas
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Pincode not found']);
    }

    // 2. Area ya City Name type karne par Reverse Search (Pincode + City + State Fetch)
    public function searchArea(Request $request)
    {
        $q = trim($request->get('q'));

        if (strlen($q) < 3) {
            return response()->json([]);
        }

        $results = DB::table('pincodes')
            ->where('office_name', 'LIKE', "%{$q}%")
            ->orWhere('district', 'LIKE', "%{$q}%")
            ->limit(25)
            ->get(['pincode', 'office_name as area', 'district', 'state_name as state']);

        return response()->json($results);
    }

    // 3. Profile & KYC Save/Update
    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name'                 => 'required|string|max:255',
            'email'                => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'mobile'               => ['nullable', 'string', 'max:20', Rule::unique('users')->ignore($user->id)],
            'gender'               => 'nullable|in:Male,Female,Other',
            'dob'                  => 'nullable|date',
            
            // Location
            'pincode'              => 'nullable|string|max:10',
            'area'                 => 'nullable|string|max:255',
            'city'                 => 'nullable|string|max:255',
            'state'                => 'nullable|string|max:255',
            'address'              => 'nullable|string|max:500',

            // Vehicle & License
            'vehicle_no'           => 'nullable|string|max:50',
            'license_no'           => 'nullable|string|max:50',

            // KYC & Files
            'pan_number'           => 'nullable|string|max:20',
            'aadhaar_number'       => 'nullable|string|max:20',
            'profile_photo'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'pan_image'            => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'aadhaar_front_image'  => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'aadhaar_back_image'   => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',

            // Bank
            'account_holder_name'  => 'nullable|string|max:255',
            'bank_name'            => 'nullable|string|max:255',
            'account_number'       => 'nullable|string|max:50',
            'ifsc_code'            => 'nullable|string|max:20',
            'upi_id'               => 'nullable|string|max:100',

            // Security
            'password'             => 'nullable|string|min:8|confirmed',
        ]);

        // Basic Info
        $user->name    = $request->name;
        $user->email   = $request->email;
        $user->mobile  = $request->mobile;
        $user->gender  = $request->gender;
        $user->dob     = $request->dob;

        // Address
        $user->pincode = $request->pincode;
        $user->area    = $request->area;
        $user->city    = $request->city;
        $user->state   = $request->state;
        $user->address = $request->address;

        // Vehicle
        $user->vehicle_no = $request->vehicle_no;
        $user->license_no = $request->license_no;

        // KYC & Bank Text
        $user->pan_number          = $request->pan_number;
        $user->aadhaar_number      = $request->aadhaar_number;
        $user->account_holder_name = $request->account_holder_name;
        $user->bank_name           = $request->bank_name;
        $user->account_number      = $request->account_number;
        $user->ifsc_code           = $request->ifsc_code;
        $user->upi_id              = $request->upi_id;

        // Handle Image Uploads
        $uploadPath = public_path('uploads/delivery_docs');
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $filename = 'profile_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadPath, $filename);
            $user->profile_photo = 'uploads/delivery_docs/' . $filename;
        }

        if ($request->hasFile('pan_image')) {
            $file = $request->file('pan_image');
            $filename = 'pan_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadPath, $filename);
            $user->pan_image = 'uploads/delivery_docs/' . $filename;
        }

        if ($request->hasFile('aadhaar_front_image')) {
            $file = $request->file('aadhaar_front_image');
            $filename = 'adh_front_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadPath, $filename);
            $user->aadhaar_front_image = 'uploads/delivery_docs/' . $filename;
        }

        if ($request->hasFile('aadhaar_back_image')) {
            $file = $request->file('aadhaar_back_image');
            $filename = 'adh_back_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadPath, $filename);
            $user->aadhaar_back_image = 'uploads/delivery_docs/' . $filename;
        }

        // Auto change KYC Status to pending if documents re-uploaded
        if ($request->hasFile('pan_image') || $request->hasFile('aadhaar_front_image') || $request->hasFile('aadhaar_back_image')) {
            $user->kyc_status = 'pending';
        }

        // Change Password if filled
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return back()->with('success', 'Profile and verification details updated successfully!');
    }
}