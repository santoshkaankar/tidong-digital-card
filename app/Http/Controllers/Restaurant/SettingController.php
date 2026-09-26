<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    // 1. Show Restaurant Settings Page
    public function index()
    {
        $restaurant = Auth::user();
        return view('vendor.restaurant.setting.settings', compact('restaurant'));
    }

    // 2. Save / Update Settings
    public function update(Request $request)
    {
        $user = Auth::user();

        // Custom Validation with GSTIN format Regex & FSSAI validation
        $request->validate([
            'name'                => 'required|string|max:255',
            'mobile'              => 'required|string|max:20',
            'food_type'           => 'required|in:pure_veg,non_veg,both',
            'pincode'             => 'required|string|max:10',
            'area'                => 'nullable|string|max:255',
            'city'                => 'required|string|max:255',
            'state'               => 'required|string|max:255',
            'address'             => 'required|string|max:500',
            
            // GST & FSSAI Verification
            'gstin'               => ['nullable', 'string', 'regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/i'],
            'fssai_number'        => 'nullable|numeric|digits:14',

            // Files & Images Validation
            'profile_photo'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'fssai_certificate'   => 'nullable|file|mimes:jpeg,png,jpg,webp,pdf|max:3072',
            'gst_certificate'     => 'nullable|file|mimes:jpeg,png,jpg,webp,pdf|max:3072',
            'mca_certificate'     => 'nullable|file|mimes:jpeg,png,jpg,webp,pdf|max:3072',
            'upi_qr_code'         => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',

            // Bank Details
            'account_holder_name' => 'nullable|string|max:255',
            'bank_name'           => 'nullable|string|max:255',
            'account_number'      => 'nullable|string|max:50',
            'ifsc_code'           => 'nullable|string|max:20',
            'upi_id'              => 'nullable|string|max:100',
        ], [
            'gstin.regex'          => 'GST Number ka format sahi nahi hai! (Format: 08ABCDE1234F1ZH)',
            'fssai_number.digits'  => 'FSSAI License number 14 digits ka hona chahiye.',
            'fssai_number.numeric' => 'FSSAI License number me sirf numbers hone chahiye.',
        ]);

        $data = $request->only([
            'name', 'mobile', 'food_type', 'pincode', 
            'area', 'city', 'state', 'address', 'gstin', 'fssai_number',
            'account_holder_name', 'bank_name', 'account_number', 'ifsc_code', 'upi_id'
        ]);

        // 1. Restaurant Profile Photo Upload
        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            $data['profile_photo'] = $request->file('profile_photo')->store('restaurants', 'public');
        }

        // 2. FSSAI Certificate File Upload
        if ($request->hasFile('fssai_certificate')) {
            if ($user->fssai_certificate && Storage::disk('public')->exists($user->fssai_certificate)) {
                Storage::disk('public')->delete($user->fssai_certificate);
            }
            $data['fssai_certificate'] = $request->file('fssai_certificate')->store('certificates/fssai', 'public');
        }

        // 3. GST Certificate File Upload
        if ($request->hasFile('gst_certificate')) {
            if ($user->gst_certificate && Storage::disk('public')->exists($user->gst_certificate)) {
                Storage::disk('public')->delete($user->gst_certificate);
            }
            $data['gst_certificate'] = $request->file('gst_certificate')->store('certificates/gst', 'public');
        }

        // 4. MCA Certificate File Upload
        if ($request->hasFile('mca_certificate')) {
            if ($user->mca_certificate && Storage::disk('public')->exists($user->mca_certificate)) {
                Storage::disk('public')->delete($user->mca_certificate);
            }
            $data['mca_certificate'] = $request->file('mca_certificate')->store('certificates/mca', 'public');
        }

        // 5. UPI QR Code Image Upload
        if ($request->hasFile('upi_qr_code')) {
            if ($user->upi_qr_code && Storage::disk('public')->exists($user->upi_qr_code)) {
                Storage::disk('public')->delete($user->upi_qr_code);
            }
            $data['upi_qr_code'] = $request->file('upi_qr_code')->store('qr_codes', 'public');
        }

        // Update User / Restaurant in Supabase DB
        $user->update($data);

        return redirect()->back()->with('success', 'Restaurant settings saved successfully!');
    }

    // 3. Location Dynamic Search API (Pincode, Area, City)
    public function searchLocation(Request $request)
    {
        $search = $request->get('term') ?? $request->get('q');

        if (empty($search)) {
            return response()->json([]);
        }

        // Exact DB column matching: office_name, district, state_name, pincode
        $locations = DB::table('pincodes')
            ->where('pincode', 'LIKE', "%{$search}%")
            ->orWhere('office_name', 'LIKE', "%{$search}%")
            ->orWhere('district', 'LIKE', "%{$search}%")
            ->orWhere('state_name', 'LIKE', "%{$search}%")
            ->limit(15)
            ->get(['pincode', 'office_name', 'district', 'state_name']);

        return response()->json($locations);
    }
}