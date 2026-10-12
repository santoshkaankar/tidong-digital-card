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

        // 1. User ke voice input se Pincode (6 digits) ya District/Area extract karne ki koshish
        preg_match('/\b\d{6}\b/', $userVoiceInput, $pincodeMatches);
        $searchedPincode = $pincodeMatches[0] ?? null;

        $matchedRestaurants = collect();

        if ($searchedPincode) {
            // Agar user ne pincode bola hai, toh pincodes table se match karke wahan ke vendors find karo (Limit 3)
            $pincodeData = DB::table('pincodes')->where('pincode', $searchedPincode)->first();
            
            if ($pincodeData) {
                // Us district ya office name ke hisab se vendors fetch karo taaki server crash na ho
                $matchedRestaurants = User::whereHas('restaurantItems')
                    ->where(function($q) use ($pincodeData) {
                        $q->where('address', 'LIKE', '%' . $pincodeData->district . '%')
                          ->orWhere('address', 'LIKE', '%' . $pincodeData->office_name . '%');
                    })
                    ->select('id', 'name', 'restaurant_name', 'address', 'city')
                    ->take(3)
                    ->get();
            }
        }

        // Agar specific pincode nahi mila ya match nahi hua, toh default safe limit (Sirf 3 restaurants) fetch karo taaki memory bachi rahe
        if ($matchedRestaurants->isEmpty()) {
            $matchedRestaurants = User::whereHas('restaurantItems')
                ->select('id', 'name', 'restaurant_name', 'address', 'city')
                ->latest()
                ->take(3)
                ->get();
        }

        $vendorId = session('vendor_id');
        $availableItems = [];
        if ($vendorId) {
            $availableItems = RestaurantItem::where('user_id', $vendorId)
                ->where('status', true)
                ->take(20) // Memory protect karne ke liye items ki limit
                ->get(['id', 'item_name', 'price']);
        }

        $apiKey = env('GEMINI_API_KEY');
        if (!$apiKey) {
            return response()->json([
                'bot_reply' => 'AI Voice service configured nahi hai.',
            ]);
        }

        // AI Prompt
        $aiPrompt = "Aap Tidong Voice Assistant hain. User ne kaha: '$userVoiceInput'.
Context:
- Nearby Restaurants Found: " . json_encode($matchedRestaurants) . "
- Menu Items: " . json_encode($availableItems) . "

Instructions:
1. User ko Hinglish (Roman Hindi) me batao ki us area me kaun se restaurants available hain (sirf 2-3 batao). Kabhi mana mat karo.
2. Agar order karna chahe toh items identify karo.
3. Jawab chhota aur clear rakho.

Strictly JSON format me return karo:
{
  \"type\": \"search\" | \"order\" | \"info\",
  \"items\": [{\"id\": item_id, \"quantity\": qty}],
  \"reply\": \"Hinglish me saaf jawab likho jo bolne me achha lage\"
}";

        $response = Http::post("https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key={$apiKey}", [
            'contents' => [
                ['parts' => [['text' => $aiPrompt]]]
            ]
        ]);

        $aiData = $response->json();
        $rawResponseText = $aiData['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
        
        $parsedJson = json_decode(preg_replace('/```json|```/', '', $rawResponseText), true);

        $botReply = $parsedJson['reply'] ?? "Hamare paas aapke area me restaurants available hain, aap kya khana chahenge?";
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
            $intentType
        ]);
    }
}