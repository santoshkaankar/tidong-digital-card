<?php

namespace App\Http\Controllers\Global;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\User; // Restaurant vendors ke liye
use App\Models\Restaurant\RestaurantItem;
use App\Models\Restaurant\RestaurantTable;

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

        // 1. Registered Restaurants / Food Search Context gather karein
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

        // 2. AI Prompt jisme restaurant search, table inquiry aur ordering sabhi handled hain
        $aiPrompt = "You are Tidong Digital Restaurant Assistant. User said: '$userVoiceInput'.
Context Data:
- Available Restaurants/Vendors: " . json_encode($restaurants) . "
- Current Vendor Menu Items: " . json_encode($availableItems) . "

Instructions:
Analyze user intent:
1. If user is asking for restaurants or food options nearby, list the registered restaurants and their food.
2. If user is asking about table availability, provide a general estimated wait time (e.g., 'Abhi 2 tables khali hain, ya 10 minute ka wait hai').
3. If user wants to order items, match them with menu items and prepare order items.

Return response STRICTLY in valid JSON format:
{
  \"type\": \"search\" | \"order\" | \"info\",
  \"items\": [{\"id\": item_id, \"quantity\": qty}],
  \"reply\": \"Hinglish natural voice response to speak to the user (keep it polite, clear and conversational under 2 sentences)\"
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

        // Agar user ka intent order karne ka hai, toh cart me add karein
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