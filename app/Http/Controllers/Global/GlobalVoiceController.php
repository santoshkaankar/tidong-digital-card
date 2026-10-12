<?php

namespace App\Http\Controllers\Global;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Restaurant\RestaurantItem;

class GlobalVoiceController extends Controller
{
    public function processVoiceCommand(Request $request)
    {
        $userVoiceInput = $request->input('message'); 
        $vendorId = session('vendor_id');

        if (!$vendorId) {
            return response()->json([
                'bot_reply' => 'Kripya pehle table ka QR scan karein.',
                'updated_cart' => []
            ]);
        }

        $availableItems = RestaurantItem::where('user_id', $vendorId)
            ->where('status', true)
            ->get(['id', 'item_name', 'price']);

        $apiKey = env('GEMINI_API_KEY');
        if (!$apiKey) {
            return response()->json([
                'bot_reply' => 'AI Voice service configured nahi hai.',
                'updated_cart' => []
            ]);
        }

        $aiPrompt = "User said: '$userVoiceInput'.
Available Menu Items: " . json_encode($availableItems) . "
Task: Identify items user wants to order. Return ONLY valid JSON format:
{
  \"items\": [{\"id\": item_id, \"quantity\": qty}],
  \"reply\": \"Short Hinglish reply to customer (e.g. Maine 1 Paneer Tikka cart me add kar diya hai)\"
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
        $orderedItems = $parsedJson['items'] ?? [];

        $cart = session()->get('cart', []);
        foreach ($orderedItems as $item) {
            $itemId = $item['id'];
            $qty = $item['quantity'];

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
        session()->put('cart', $cart);

        return response()->json([
            'bot_reply' => $botReply,
            'cart_count' => count($cart)
        ]);
    }
}