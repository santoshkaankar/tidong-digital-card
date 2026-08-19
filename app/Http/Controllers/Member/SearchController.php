<?php

namespace App\Http\Controllers\Member;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        return $this->search($request);
    }

    public function search(Request $request)
    {
        // 1. visiting_cards table se unique states fetch karna dropdown ke liye
        $states = DB::table('visiting_cards')
                    ->whereNotNull('state')
                    ->where('state', '!=', '')
                    ->orderBy('state', 'asc')
                    ->distinct()
                    ->pluck('state');

        // 2. Selected State ke mutabiq cities fetch karna dropdown ke liye
        $cities = collect();
        if ($request->filled('state')) {
            $cities = DB::table('visiting_cards')
                        ->where('state', $request->state)
                        ->whereNotNull('city')
                        ->where('city', '!=', '')
                        ->orderBy('city', 'asc')
                        ->distinct()
                        ->pluck('city');
        }

        // 3. Search Logic - Jab tak search keyword ya city na ho, tab tak results blank rahenge
        $hasSearched = $request->filled('search_name') || $request->filled('city');

        $results = collect();

        if ($hasSearched) {
            $query = DB::table('visiting_cards');

            // Keyword search across Name, Card No, Phone, WhatsApp, Gmail, and Business Name
            if ($request->filled('search_name')) {
                $searchTerm = $request->search_name;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('name', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('card_no', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('phone', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('whatsapp', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('gmail', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('business_name', 'LIKE', "%{$searchTerm}%");
                });
            }

            // State filter
            if ($request->filled('state')) {
                $query->where('state', $request->state);
            }

            // City filter
            if ($request->filled('city')) {
                $query->where('city', $request->city);
            }

            $results = $query->limit(50)->get();
        }

        return view('member.search', compact('states', 'cities', 'results', 'hasSearched'));
    }
}