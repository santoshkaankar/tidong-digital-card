<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\RestaurantTable;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class TableController extends Controller
{
    public function index()
    {
        $tables = RestaurantTable::where('user_id', Auth::id())->latest()->get();
        return view('vendor.restaurant.tables.index', compact('tables'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'table_number' => 'required|string|max:100',
            'seating_capacity' => 'nullable|integer|min:1'
        ]);

        RestaurantTable::create([
            'user_id' => Auth::id(),
            'table_number' => $request->table_number,
            'seating_capacity' => $request->seating_capacity ?? 4,
            'qr_code_token' => Str::random(32),
            'status' => 'available'
        ]);

        return redirect()->back()->with('success', 'Table & QR generated successfully!');
    }

    public function destroy($id)
    {
        $table = RestaurantTable::where('user_id', Auth::id())->findOrFail($id);
        $table->delete();
        return redirect()->back()->with('success', 'Table removed successfully!');
    }
}