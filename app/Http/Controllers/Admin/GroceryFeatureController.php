<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Grocery\GroceryAccess;
use App\Services\Grocery\GroceryFeatures;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

/** Super Admin controls all Grocery options independently. */
final class GroceryFeatureController extends Controller
{
    public function index(): View
    {
        abort_unless(Schema::hasTable('grocery_feature_flags'), 503, 'Grocery database migration is not installed.');
        $settings = DB::table('grocery_feature_flags')->pluck('enabled', 'feature');
        return view('admin.grocery.features', ['labels' => GroceryFeatures::LABELS, 'settings' => $settings]);
    }

    public function update(Request $request, string $feature): RedirectResponse
    {
        abort_unless(array_key_exists($feature, GroceryFeatures::LABELS), 404);
        abort_unless(Schema::hasTable('grocery_feature_flags'), 503, 'Grocery database migration is not installed.');
        $data = $request->validate(['enabled' => ['required', 'boolean']]);
        $enabled = (bool) $data['enabled'];
        DB::transaction(function () use ($request, $feature, $enabled): void {
            $old = DB::table('grocery_feature_flags')->where('feature', $feature)->lockForUpdate()->first();
            DB::table('grocery_feature_flags')->updateOrInsert(['feature' => $feature], [
                'enabled' => $enabled, 'updated_by' => $request->user()->id,
                'created_at' => $old?->created_at ?? now(), 'updated_at' => now(),
            ]);
            GroceryAccess::audit($request, 'grocery.feature.changed', 'grocery_feature', null, [
                'feature' => $feature, 'old_enabled' => $old ? (bool) $old->enabled : false,
                'enabled' => $enabled,
            ]);
        });
        return back()->with('success', 'Grocery feature setting saved.');
    }
}
