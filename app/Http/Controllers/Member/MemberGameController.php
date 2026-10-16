<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Member\MarketItem;
use App\Models\Wallet;
use App\Models\GameInventory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MemberGameController extends Controller
{
    public function proIndex()
    {
        $user = Auth::user();

        // 1. Check Active Subscription from game_subscriptions table
        $subscription = DB::table('game_subscriptions')
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->first();

        // Agar subscription active nahi hai, toh subscription page par redirect karein
        if (!$subscription) {
            return redirect()->route('member.wallet')->with('error', 'Pehle 3D Game Pro ka Yearly Pass (199 T-Coins) unlock karein!');
        }

        // 2. Fetch or Create Secure Wallet Database Record
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['t_coins' => 25000.00, 'real_balance' => 0.00]
        );

        // 3. Fetch Market Items categorized for Showrooms
        $marketItems = MarketItem::all()->groupBy('category');

        // 4. Fetch User Inventory (Saved items, tools, cars, etc.)
        $inventory = GameInventory::where('user_id', $user->id)->get();

        return view('member.game.pro', compact('wallet', 'marketItems', 'inventory', 'subscription'));
    }

    public function buyItem(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:market_items,id'
        ]);

        $user = Auth::user();
        $item = MarketItem::findOrFail($request->item_id);
        $wallet = Wallet::where('user_id', $user->id)->first();

        if ($wallet->t_coins < $item->price) {
            return response()->json([
                'status' => 'error',
                'message' => 'T-Coins kam hain! Website se INR ke through T-Coins recharge karein.'
            ], 400);
        }

        // Deduct T-Coins
        covariance: $wallet->t_coins -= $item->price;
        $wallet->save();

        // Save into game_inventories table permanently
        GameInventory::create([
            'user_id' => $user->id,
            'item_name' => $item->name,
            'item_type' => $item->item_type,
            'quantity' => 1,
            'durability' => 100,
            'value' => $item->price
        ]);

        // Log Transaction
        DB::table('t_coin_transactions')->insert([
            'user_id' => $user->id,
            'amount' => $item->price,
            'type' => 'debit',
            'description' => 'Purchased ' . $item->name . ' in 3D Game Pro World',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json([
            'status' => 'success',
            'message' => $item->name . ' safaltapoorvak kharid liya gaya aur inventory me save ho gaya!',
            'new_balance' => $wallet->t_coins,
            'item' => $item
        ]);
    }

    public function completeTask(Request $request)
    {
        $request->validate([
            'reward' => 'required|numeric'
        ]);

        $user = Auth::user();
        $wallet = Wallet::where('user_id', $user->id)->first();
        $wallet->t_coins += $request->reward;
        $wallet->save();

        // Log Task Earning Transaction
        DB::table('t_coin_transactions')->insert([
            'user_id' => $user->id,
            'amount' => $request->reward,
            'type' => 'credit',
            'description' => 'Reward earned from 3D Game Pro task completion',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Task complete! +' . $request->reward . ' T-Coins wallet me jud gaye.',
            'new_balance' => $wallet->t_coins
        ]);
    }
}