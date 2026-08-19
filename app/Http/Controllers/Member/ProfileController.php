<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Member\VisitingCard;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        $card = VisitingCard::where('user_id', $user->id)->first();
        
        // Temporary dummy variables jab tak model nahi bante
        $walletBalance = 0.00;
        $activePlan = null; // ya koi array/object agar test karna ho
        
        // Views & Shares (Agar visiting_card table mein views/shares columns hain)
        $profileViews = $card && isset($card->views) ? $card->views : 1245;
        $cardShares = $card && isset($card->shares) ? $card->shares : 348;

        return view('member.profile', compact('card', 'walletBalance', 'activePlan', 'profileViews', 'cardShares'));
    }

    public function edit()
    {
        $user = Auth::user();
        $card = VisitingCard::where('user_id', $user->id)->first();
        return view('member.card.configure', compact('card'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $card = VisitingCard::where('user_id', $user->id)->first();

        if (!$card) {
            $card = new VisitingCard();
            $card->user_id = $user->id;
        }

        if ($request->hasFile('profile_photo')) {
            if ($card->profile_photo && Storage::disk('public')->exists($card->profile_photo)) {
                Storage::disk('public')->delete($card->profile_photo);
            }
            $card->profile_photo = $request->file('profile_photo')->store('profiles', 'public');
        }

        if ($request->hasFile('banner_image')) {
            if ($card->banner_image && Storage::disk('public')->exists($card->banner_image)) {
                Storage::disk('public')->delete($card->banner_image);
            }
            $card->banner_image = $request->file('banner_image')->store('banners', 'public');
        }

        $card->fill($request->except(['profile_photo', 'banner_image']));
        $card->save();

        return redirect()->back()->with('success', 'Profile and banner updated successfully!');
    }
}