<?php

namespace App\Http\Controllers\Retail;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Retail\Product;
use App\Models\Retail\Category;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->has('category')) {
            $query->where('category_id', $request->category);
        }

        $products = $query->paginate(12);
        $categories = Category::all();

        return view('retail.shop.index', compact('products', 'categories'));
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);
        return view('retail.shop.show', compact('product'));
    }
}