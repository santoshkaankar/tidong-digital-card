<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Restaurant\RestaurantCategory;
use App\Models\Restaurant\RestaurantItem;
use Illuminate\Support\Facades\Schema;

class RestaurantController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $type = $request->input('type');

        $query = User::query();

        if (Schema::hasColumn('users', 'role')) {
            $query->whereIn('role', ['vendor', 'restaurant', 'vendor_restaurant']);
        }

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%");

                if (Schema::hasColumn('users', 'city')) {
                    $q->orWhere('city', 'LIKE', "%{$search}%");
                }
                if (Schema::hasColumn('users', 'state')) {
                    $q->orWhere('state', 'LIKE', "%{$search}%");
                }
            });
        }

        if (!empty($type) && $type !== 'all') {
            if (Schema::hasColumn('users', 'food_type')) {
                if ($type === 'pureveg' || $type === 'veg') {
                    $query->whereIn('food_type', ['pure_veg', 'veg', 'pureveg']);
                } elseif ($type === 'nonveg') {
                    $query->whereIn('food_type', ['non_veg', 'nonveg']);
                }
            }
        }

        $restaurants = $query->latest()->paginate(12)->withQueryString();

        return view('member.restaurant.index', compact('restaurants', 'search', 'type'));
    }

    public function show($id)
    {
        $restaurant = User::findOrFail($id);

        // Fetch categories
        $categories = RestaurantCategory::query();
        if (Schema::hasColumn('restaurant_categories', 'user_id')) {
            $categories->where('user_id', $id);
        } elseif (Schema::hasColumn('restaurant_categories', 'restaurant_id')) {
            $categories->where('restaurant_id', $id);
        }
        $categories = $categories->get();

        // Fetch food items
        $itemsQuery = RestaurantItem::query();
        if (Schema::hasColumn('restaurant_items', 'user_id')) {
            $itemsQuery->where('user_id', $id);
        } elseif (Schema::hasColumn('restaurant_items', 'restaurant_id')) {
            $itemsQuery->where('restaurant_id', $id);
        }
        $items = $itemsQuery->where('status', 1)->get();

        return view('member.restaurant.show', compact('restaurant', 'categories', 'items'));
    }
}