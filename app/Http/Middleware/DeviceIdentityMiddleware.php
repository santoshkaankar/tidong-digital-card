<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\GuestSession;

class DeviceIdentityMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Cookie या Header से Device UUID निकालें
        $deviceUuid = $request->cookie('tidong_device_id');

        if (!$deviceUuid) {
            // अगर नया यूजर है, तो नई Unique UUID जनरेट करें
            $deviceUuid = (string) Str::uuid();
            cookie()->queue(cookie()->forever('tidong_device_id', $deviceUuid));
        }

        // DB में Device Session रजिस्टर या अपडेट करें
        $session = GuestSession::firstOrCreate(
            ['device_uuid' => $deviceUuid],
            ['language' => 'en', 'ip_address' => $request->ip()]
        );

        // QR स्कैन कोड का डेटा ट्रैक करें
        if ($request->has('vendor_id')) {
            $session->vendor_id = $request->vendor_id;
            $session->last_table_or_room = $request->get('table', null);
            $session->save();
        }

        // रिक्वेस्ट में session अटेच करें
        $request->attributes->set('guest_session', $session);

        return $next($request);
    }
}