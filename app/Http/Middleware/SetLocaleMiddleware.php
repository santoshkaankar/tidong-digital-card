<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocaleMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (Session::has('locale')) {
            App::setLocale(Session::get('locale'));
        } else {
            // Supported languages list (Global Tourists & Local Users)
            $supportedLocales = [
                'en', 'hi', 'es', 'fr', 'de', 'ja', 'zh', 'ar', 'ru', 'pt', 
                'it', 'ko', 'bn', 'ta', 'te', 'mr', 'gu', 'kn', 'pa', 'ur'
            ];

            // Auto-detect browser/phone language
            $preferredLanguage = $request->getPreferredLanguage($supportedLocales);
            $locale = $preferredLanguage ?? 'en';

            App::setLocale($locale);
            Session::put('locale', $locale);
        }

        return $next($request);
    }
}