<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IndiaPostDirectoryController extends Controller
{
    public function options(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'level' => ['required', 'in:states,districts,offices'],
            'state' => ['required_unless:level,states', 'nullable', 'string', 'max:100'],
            'district' => ['required_if:level,offices', 'nullable', 'string', 'max:150'],
        ]);
        $directory = DB::table('india_post_offices');

        if ($validated['level'] === 'states') {
            return response()->json(['options' => $directory->whereNotIn('state', ['', 'NA'])
                ->distinct()->orderBy('state')->pluck('state')]);
        }
        $directory->where('state', $validated['state']);
        if ($validated['level'] === 'districts') {
            return response()->json(['options' => $directory->whereNotIn('district', ['', 'NA'])
                ->distinct()->orderBy('district')->pluck('district')]);
        }
        return response()->json(['options' => $directory->where('district', $validated['district'])
            ->orderBy('post_office')->orderBy('pincode')
            ->limit(1500)->get(['id', 'post_office', 'pincode', 'delivery_type'])]);
    }
}
