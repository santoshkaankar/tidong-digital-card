<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\Restaurant\RestaurantCategory;
use App\Models\Restaurant\RestaurantItem;
use App\Models\Restaurant\RestaurantCustomItem;
use App\Models\Restaurant\RestaurantTable;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MenuCardController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $user = auth()->user();

        // Active Categories and Items fetch with globalItem relationship
        $categories = RestaurantCategory::where('user_id', $userId)
            ->with(['items' => function ($query) use ($userId) {
                $query->where('user_id', $userId)
                      ->where('status', true)
                      ->with('globalItem');
            }])
            ->get();

        // Fetch custom items so they appear in catalogs / menu cards
        $customItems = RestaurantCustomItem::where('user_id', $userId)
            ->where('is_available', true)
            ->with(['category'])
            ->get();

        // Dynamic Tables/Catalogs fetch
        $tables = RestaurantTable::where('user_id', $userId)->latest()->get();

        return view('vendor.restaurant.menu-card.index', compact('categories', 'customItems', 'user', 'tables'));
    }

    /**
     * Create New Catalog
     */
    public function store(Request $request)
    {
        $request->validate([
            'table_name' => 'required|string|max:255',
            'items' => 'nullable|array',
        ]);

        RestaurantTable::create([
            'user_id' => auth()->id(),
            'table_number' => $request->table_name,
            'qr_code_token' => Str::random(12),
            'selected_items' => $request->items ?? [],
        ]);

        return redirect()->back()->with('success', 'Catalog successfully created!');
    }

    /**
     * Delete Catalog
     */
    public function destroy($id)
    {
        $table = RestaurantTable::where('user_id', auth()->id())->findOrFail($id);
        $table->delete();

        return redirect()->back()->with('success', 'Catalog successfully deleted!');
    }

    /**
     * Copy / Duplicate Catalog
     */
    public function copy($id)
    {
        $userId = auth()->id();
        $original = RestaurantTable::where('user_id', $userId)->findOrFail($id);

        $oldName = $original->table_number ?? 'Table 1';
        $newName = preg_replace_callback('/\d+$/', function($m) {
            return ((int)$m[0] + 1);
        }, $oldName);

        if ($newName === $oldName) {
            $newName = $oldName . ' (Copy)';
        }

        RestaurantTable::create([
            'user_id' => $userId,
            'table_number' => $newName,
            'qr_code_token' => Str::random(12),
            'selected_items' => $original->selected_items ?? [],
        ]);

        return redirect()->back()->with('success', 'Catalog copy ho gaya! Ab aap ise Edit kar sakte hain.');
    }

    /**
     * Update Existing Catalog
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'table_name' => 'required|string|max:255',
            'items' => 'nullable|array',
        ]);

        $catalog = RestaurantTable::where('user_id', auth()->id())->findOrFail($id);
        
        $catalog->update([
            'table_number' => $request->table_name,
            'selected_items' => $request->items ?? [],
        ]);

        return redirect()->back()->with('success', 'Catalog safaltapoorvak update ho gaya!');
    }
}