<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class MobilePasswordResetController extends Controller
{
    // 1. Show Mobile Input Form
    public function showMobileForm()
    {
        return view('auth.forgot-password-mobile');
    }

    // 2. Generate & Send OTP
    public function sendOtp(Request $request)
    {
        $request->validate([
            'mobile' => 'required|numeric|digits:10|exists:users,mobile'
        ], [
            'mobile.exists' => 'This mobile number is not registered.'
        ]);

        $user = User::where('mobile', $request->mobile)->first();

        // Generate 4-digit OTP
        $otp = rand(1000, 9999);

        // Store OTP with 10 minutes expiry
        $user->otp = $otp;
        $user->otp_expires_at = Carbon::now()->addMinutes(10);
        $user->save();

        // SMS Gateway Integration (Fast2SMS / Twilio / MSG91)
        // $this->sendSmsApi($user->mobile, "Your Password Reset OTP is: " . $otp);

        return redirect()->route('password.verify.form', ['mobile' => $user->mobile])
                         ->with('success', 'OTP has been sent to your mobile number.');
    }

    // 3. Show OTP Verification Form
    public function showVerifyForm($mobile)
    {
        return view('auth.verify-otp', compact('mobile'));
    }

    // 4. Verify OTP & Reset Password
    public function resetPassword(Request $request)
    {
        $request->validate([
            'mobile' => 'required|numeric|exists:users,mobile',
            'otp' => 'required|numeric|digits:4',
            'password' => 'required|min:6|confirmed'
        ]);

        $user = User::where('mobile', $request->mobile)
                    ->where('otp', $request->otp)
                    ->where('otp_expires_at', '>=', Carbon::now())
                    ->first();

        if (!$user) {
            return back()->withErrors(['otp' => 'Invalid OTP or OTP has expired.']);
        }

        // Update password & clear OTP fields
        $user->password = Hash::make($request->password);
        $user->otp = null;
        $user->otp_expires_at = null;
        $user->save();

        return redirect()->route('login')->with('success', 'Password reset successfully! You can now log in.');
    }
}