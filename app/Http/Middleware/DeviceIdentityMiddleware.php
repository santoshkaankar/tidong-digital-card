<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\GuestSession;
use App\Models\User; // ya aapka jo bhi User model ho

class DeviceIdentityMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // 1. Cookie ya Header se Device UUID nikalen
        $deviceUuid = $request->cookie('tidong_device_id');

        if (!$deviceUuid) {
            // Agar naya user hai, toh nayi Unique UUID generate karen
            $deviceUuid = (string) Str::uuid();
            cookie()->queue(cookie()->forever('tidong_device_id', $deviceUuid));
        }

        // 2. DB me Device Session register ya update karen
        $session = GuestSession::firstOrCreate(
            ['device_uuid' => $deviceUuid],
            ['language' => 'en', 'ip_address' => $request->ip()]
        );

        // 3. Auto-detect Registered User Logic
        // Agar user pehle se web guard par logged in nahi hai, lekin session me user_id link hai ya device se match hota hai
        if (!Auth::check()) {
            // Agar pehle se is device/session ke sath koi user_id mapped hai toh auto-login karwa sakte hain
            if (!empty($session->user_id)) {
                $registeredUser = User::find($session->user_id);
                if ($registeredUser) {
                    Auth::login($registeredUser);
                }
            }
        } else {
            // Agar user ab logged in hai, toh guest session ke sath user_id link kar dein taaki record track ho sake
            if (empty($session->user_id)) {
                $session->user_id = Auth::id();
                $session->save();
            }
        }

        // 4. QR scan code ka data track karen[cite: 5]
        if ($request->has('vendor_id') || $request->route('token')) {
            $session->vendor_id = $request->get('vendor_id', $session->vendor_id);
            if ($request->has('table')) {
                $session->last_table_or_room = $request->get('table', null);
            }
            $session->save();
        }

        // 5. Request me session attach karen[cite: 5]
        $request->attributes->set('guest_session', $session);

        return $next($request);
    }
}