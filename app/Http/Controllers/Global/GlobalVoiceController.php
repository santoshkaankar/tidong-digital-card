<?php

namespace App\Http\Controllers\Global;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Restaurant\RestaurantItem;

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

        // 1. Safe fetch: Database se saare available registered restaurants nikal lo (Max 5) taaki kabhi empty na ho
        $allRestaurants = User::whereHas('restaurantItems')
            ->select('id', 'name', 'restaurant_name', 'address', 'city')
            ->take(5)
            ->get();

        $vendorId = session('vendor_id');
        $availableItems = [];
        if ($vendorId) {
            $availableItems = RestaurantItem::where('user_id', $vendorId)
                ->where('status', true)
                ->take(30)
                ->get(['id', 'item_name', 'price']);
        }

        $apiKey = env('GEMINI_API_KEY');
        if (!$apiKey) {
            return response()->json([
                'bot_reply' => 'AI Voice service configured nahi hai.',
            ]);
        }

        // 2. Powerful Assistant Prompt: Kabhi mana nahi karega, hamesha friendly baat karega
        $aiPrompt = "Aap Tidong ke smart AI restaurant assistant hain. User ne pucha hai: '$userVoiceInput'.
Context Data:
- Platform par registered available restaurants: " . json_encode($allRestaurants) . "
- Current Menu Items: " . json_encode($availableItems) . "

Instructions for Assistant:
1. Aapko kabhi bhi 'restaurant nahi hai' ya 'mafi chahta hu' bolkar mana nahi karna hai. 
2. Agar user kuch bhi puche (jaise khana kahan milega, door ke restaurants, ya general help), toh upar diye gaye registered restaurants me se 2-3 ke naam aur unki details batao. Agar koi aur sawal ho toh ek friendly smart assistant ki tarah Hinglish me jawab do.
3. Agar user order karne ko bole toh items identify karo.

Strictly JSON format me return karo:
{
  \"type\": \"search\" | \"order\" | \"info\",
  \"items\": [{\"id\": item_id, \"quantity\": qty}],
  \"reply\": \"Yahan ekdum natural, friendly aur conversational Hinglish jawab likho jo bolne me ek real assistant jaisa lage (under 2 sentences)\"
}";

        $response = Http::post("https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key={$apiKey}", [
            'contents' => [
                ['parts' => [['text' => $aiPrompt]]]
            ]
        ]);

        $aiData = $response->json();
        $rawResponseText = $aiData['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
        
        $parsedJson = json_decode(preg_replace('/```json|```/', '', $rawResponseText), true);

        $botReply = $parsedJson['reply'] ?? "Haanji, hamare paas kai behtareen restaurants registered hain, aap bataiye kya khana pasand karenge?";
        $intentType = $parsedJson['type'] ?? 'info';
        $orderedItems = $parsedJson['items'] ?? [];

        if ($intentType == 'order' && !empty($orderedItems) && $vendorId) {
            $cart = session()->get('cart', []);
            foreach ($orderedItems as $item) {
                $itemId = $item['id'] ?? null;
                $qty = $item['quantity'] ?? 1;

                if ($itemId) {
                    if (isset($cart[$itemId])) {
                        $cart[$itemId]['quantity'] += $qty;
                    } else {
                        $dbItem = RestaurantItem::find($itemId);
                        if ($dbItem) {
                            $cart[$itemId] = [
                                'name' => $dbItem->item_name,
                                'price' => $dbItem->price,
                                'quantity' => $qty
                            ];
                        }
                    }
                }
            }
            session()->put('cart', $cart);
        }

        return response()->json([
            'bot_reply' => $botReply,
            'intent' => $intentType
        ]);
    }
}