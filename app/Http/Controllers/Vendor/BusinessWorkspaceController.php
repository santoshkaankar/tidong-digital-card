<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Vendor\Vendor;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Read-only, owner-scoped Business overview. Does not change any business data. */
class BusinessWorkspaceController extends Controller
{
    public function show(Request $request, int $business): View
    {
        $vendor = Vendor::query()->where('user_id', $request->user()->id)->firstOrFail();
        $owned = $vendor->businesses()->with(['branches' => fn ($query) => $query->orderBy('name')])
            ->findOrFail($business);
        $branches = $owned->branches;

        $selected = $request->query('branch');
        if ($selected !== null && (! is_string($selected) || ! ctype_digit($selected))) {
            abort(404, 'Branch does not belong to this Business.');
        }
        $branch = $selected === null ? $branches->first()
            : $branches->first(fn ($row) => (string) $row->id === $selected);
        if ($selected !== null && ! $branch) {
            abort(404, 'Branch does not belong to this Business.');
        }

        return view('vendor.business-workspace', [
            'vendor' => $vendor,
            'business' => $owned,
            'navBusiness' => $owned,
            'navBranch' => $branch,
            'branches' => $branches,
            'navBusinesses' => $vendor->businesses()->orderBy('name')->get(),
        ]);
    }
}
