<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\Admin\ItemCategory;
use App\Models\Restaurant\RestaurantCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        // Vendor dwara pehle se select ki gayi categories
        $categories = RestaurantCategory::where('user_id', $userId)->latest()->get();

        // Already selected IDs list
        $alreadySelectedIds = $categories->pluck('category_id')->filter()->toArray();

        // Remaining global categories
        $globalCategories = ItemCategory::whereNotIn('id', $alreadySelectedIds)->get();

        return view('vendor.restaurant.categories.index', compact('categories', 'globalCategories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'global_category_id' => 'required|exists:item_categories,id',
        ]);

        $userId = auth()->id();
        $globalCategory = ItemCategory::findOrFail($request->global_category_id);

        RestaurantCategory::create([
            'user_id'     => $userId,
            'category_id' => $globalCategory->id,
            'name'        => $globalCategory->name,
            'slug'        => Str::slug($globalCategory->name),
            'status'      => true,
        ]);

        return redirect()->back()->with('success', 'Category added successfully!');
    }

    public function destroy($id)
    {
        $category = RestaurantCategory::where('user_id', auth()->id())->findOrFail($id);
        $category->delete();

        return redirect()->back()->with('success', 'Category removed successfully!');
    }
}