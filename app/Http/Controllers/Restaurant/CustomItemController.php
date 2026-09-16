<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\Restaurant\RestaurantCategory;
use App\Models\Restaurant\RestaurantCustomItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CustomItemController extends Controller
{
    // Show Form for Creating Custom Item / Thali
    public function create()
    {
        $userId = auth()->id();
        
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Please login first.');
        }

        $categories = RestaurantCategory::where('user_id', $userId)->where('status', true)->get();
        $taxes = DB::table('taxes')->where('is_active', 1)->get();

        return view('vendor.restaurant.items.create_custom', compact('categories', 'taxes'));
    }

    // Store Custom Item / Thali in 'restaurant_custom_items' table with image
    public function store(Request $request)
    {
        $userId = auth()->id();
        if (!$userId) {
            return redirect()->route('login');
        }

        $request->validate([
            'name'                   => 'required|string|max:255',
            'restaurant_category_id' => 'required|exists:restaurant_categories,id',
            'type'                   => 'required|in:veg,non-veg,egg',
            'tax_id'                 => 'nullable|exists:taxes,id',
            'mrp'                    => 'required|numeric|min:0',
            'price'                  => 'nullable|numeric|min:0',
            'description'            => 'nullable|string|max:500',
            'image'                  => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $mrp = $request->mrp;
        $price = ($request->filled('price') && $request->price > 0) ? $request->price : $mrp;

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('restaurant/custom-items', 'public');
        }

        RestaurantCustomItem::create([
            'user_id'                => $userId,
            'restaurant_category_id' => $request->restaurant_category_id,
            'name'                   => $request->name,
            'type'                   => $request->type,
            'tax_id'                 => $request->tax_id,
            'mrp'                    => $mrp,
            'price'                  => $price,
            'description'            => $request->description,
            'image'                  => $imagePath,
            'is_available'           => true,
        ]);

        return redirect()->route('vendor.restaurant.items.index')
                         ->with('success', 'Custom Thali/Item with image successfully created and saved!');
    }

    // Delete Custom Item / Thali and its image
    public function destroy($id)
    {
        $item = RestaurantCustomItem::where('user_id', auth()->id())->findOrFail($id);
        
        if ($item->image && Storage::disk('public')->exists($item->image)) {
            Storage::disk('public')->delete($item->image);
        }

        $item->delete();
        
        return redirect()->back()->with('success', 'Custom item removed successfully!');
    }
}