<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\TranslationEngineService;
use App\Models\GuestSession;

class HubController extends Controller
{
    public function index(Request $request)
    {
        $guestSession = $request->attributes->get('guest_session');
        $currentLang = $request->get('lang', $guestSession->language ?? 'en');

        // अगर भाषा चेंज की गई हो तो अपडेट करें
        if ($request->has('lang')) {
            $guestSession->language = $currentLang;
            $guestSession->save();
        }

        return view('customer.hub', compact('guestSession', 'currentLang'));
    }
}