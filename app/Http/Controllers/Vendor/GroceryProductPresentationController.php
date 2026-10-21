<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Services\Grocery\GroceryAccess as Access;
use App\Services\Grocery\GroceryFeatures as Features;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/** Optional storefront presentation metadata. No image uploads or remote fetches. */
final class GroceryProductPresentationController extends Controller
{
    public function update(Request $request, int $business, int $product): RedirectResponse
    {
        $owned = Access::business($request, $business);
        $data = $request->validate([
            'branch_id' => ['required', 'integer'],
            'brand' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:2000'],
            'image_url' => ['nullable', 'url', 'max:1500', 'starts_with:https://'],
            'is_featured' => ['required', 'boolean'],
        ]);
        $branch = Access::branch($owned, (int) $data['branch_id']);
        Features::requireEnabled('catalogue', $branch->id);
        DB::transaction(function () use ($request, $owned, $branch, $product, $data): void {
            $p = DB::table('grocery_products')->where('id', $product)
                ->where('business_id', $owned->id)->where('branch_id', $branch->id)
                ->lockForUpdate()->firstOrFail();
            DB::table('grocery_products')->where('id', $p->id)->update([
                'brand' => $data['brand'] ?? null,
                'description' => $data['description'] ?? null,
                'image_url' => $data['image_url'] ?? null,
                'is_featured' => (bool) $data['is_featured'], 'updated_at' => now(),
            ]);
            Access::audit($request, 'grocery.product.presentation', 'grocery_product', $p->id,
                ['has_image' => filled($data['image_url'] ?? null), 'featured' => (bool) $data['is_featured']]);
        });
        return redirect()->route('vendor.grocery.index', ['business' => $business, 'branch' => $branch->id])
            ->with('success', 'Storefront product information saved.');
    }
}
