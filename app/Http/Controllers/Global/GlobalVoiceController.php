<?php

namespace App\Http\Controllers\Global;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
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

        $restaurants = User::whereHas('restaurantItems')->select('id', 'name', 'restaurant_name', 'address')->take(5)->get();
        $vendorId = session('vendor_id');
        
        $availableItems = [];
        if ($vendorId) {
            $availableItems = RestaurantItem::where('user_id', $vendorId)
                ->where('status', true)
                ->get(['id', 'item_name', 'price']);
        }

        $apiKey = env('GEMINI_API_KEY');
        if (!$apiKey) {
            return response()->json([
                'bot_reply' => 'AI Voice service configured nahi hai.',
            ]);
        }

        $aiPrompt = "Aap Tidong Voice Assistant hain. User ne kaha: '$userVoiceInput'.
Context:
- Restaurants: " . json_encode($restaurants) . "
- Menu Items: " . json_encode($availableItems) . "

Instructions:
User ke sawal ka jawab Hinglish (Roman Hindi) me do. Agar khana mangana hai toh items identify karo.
Strictly JSON format me return karo:
{
  \"type\": \"search\" | \"order\" | \"info\",
  \"items\": [{\"id\": item_id, \"quantity\": qty}],
  \"reply\": \"Yahan Hinglish me chhota aur saaf jawab likho jo bolne me achha lage (jaise: 'Maine 1 Paneer Tikka cart me add kar diya hai' ya 'Hamare paas yeh restaurants available hain')\"
}";

        $response = Http::post("https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key={$apiKey}", [
            'contents' => [
                ['parts' => [['text' => $aiPrompt]]]
            ]
        ]);

        $aiData = $response->json();
        $rawResponseText = $aiData['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
        
        $parsedJson = json_decode(preg_replace('/```json|```/', '', $rawResponseText), true);

        $botReply = $parsedJson['reply'] ?? "Samajh nahi aaya, kripya firse boliye.";
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