<?php

namespace App\Http\Controllers\Global;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class GlobalVoiceController extends Controller
{
    public function processVoiceCommand(Request $request)
    {
        $userVoiceInput = $request->input('message'); 

        if (empty($userVoiceInput)) {
            return response()->json([
                'bot_reply' => 'Maine kuch suna nahi, kripya dobara boliye.',
            ]);
        }

        // Database se registered restaurants fetch karein
        $allRestaurants = User::whereHas('restaurantItems')
            ->select('id', 'name', 'restaurant_name', 'address', 'city')
            ->take(5)
            ->get();

        $restaurantListText = "";
        foreach($allRestaurants as $res) {
            $name = $res->restaurant_name ?? $res->name ?? 'Restaurant';
            $city = $res->city ?? 'Nearby';
            $restaurantListText .= "- {$name} ({$city})\n";
        }

        $apiKey = env('GEMINI_API_KEY');

        if (!$apiKey) {
            return response()->json([
                'bot_reply' => 'Gemini API Key missing hai, kripya Render environment check karein.',
            ]);
        }

        $aiPrompt = "Aap Tidong ke smart AI restaurant assistant hain. User ne pucha hai: '$userVoiceInput'.
Hamaare paas ye restaurants registered hain:
{$restaurantListText}

Aapko kabhi bhi 'restaurant nahi hai' ya 'mafi chahta hu' bolkar mana nahi karna hai. Hamesha friendly Hinglish me 1 ya 2 lines me seedha jawab do. Agar koi restaurant ya khana puche toh upar diye gaye list me se naam lekar batao.";

        try {
            // Gemini API Call
            $response = Http::post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}", [
                'contents' => [
                    ['parts' => [['text' => $aiPrompt]]]
                ]
            ]);

            $aiData = $response->json();
            $botReply = $aiData['candidates'][0]['content']['parts'][0]['text'] ?? null;

            if ($botReply) {
                return response()->json([
                    'bot_reply' => trim($botReply),
                    'intent' => 'info'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Gemini API Error: ' . $e->getMessage());
        }

        // Fallback Reply (Agar API kisi wajeh se response na de)
        return response()->json([
            'bot_reply' => "Haanji, hamare platform par behtareen restaurants available hain. Aap kya khana pasand karenge?",
            'intent' => 'info'
        ]);
    }
}