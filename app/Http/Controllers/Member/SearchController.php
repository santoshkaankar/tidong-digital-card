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
        // 1. Pincodes table se unique states fetch karna dropdown ke liye
        $states = DB::table('pincodes')
                    ->whereNotNull('state_name')
                    ->where('state_name', '!=', '')
                    ->orderBy('state_name', 'asc')
                    ->distinct()
                    ->pluck('state_name');

        // 2. Selected State ke mutabiq districts (cities) fetch karna dropdown ke liye
        $cities = collect();
        if ($request->filled('state')) {
            $cities = DB::table('pincodes')
                        ->where('state_name', $request->state)
                        ->whereNotNull('district')
                        ->where('district', '!=', '')
                        ->orderBy('district', 'asc')
                        ->distinct()
                        ->pluck('district');
        }

        // 3. Search Logic Flag
        $hasSearched = $request->filled('search_name') || $request->filled('state') || $request->filled('city') || $request->filled('category');

        $results = collect();

        if ($hasSearched) {
            // Main search query users table par chalegi
            $query = DB::table('users');

            // 4. Admin aur Employee ko kisi bhi haalat mein search mein nahi aane dena
            $query->whereNotIn('role', ['admin', 'employee']);

            // 5. Category & Role Filter Logic
            if ($request->filled('category')) {
                $category = strtolower(trim($request->category));

                if ($category == 'members' || $category == 'member') {
                    $query->where('role', 'member');
                } 
                elseif ($category != 'all' && $category != '') {
                    $businessType = rtrim($category, 's'); // restaurants -> restaurant
                    
                    $query->where(function($q) use ($category, $businessType) {
                        $q->where('role', 'business')
                          ->where(function($subQ) use ($category, $businessType) {
                              $subQ->where('business_type', 'LIKE', "%{$category}%")
                                   ->orWhere('business_type', 'LIKE', "%{$businessType}%");
                          });
                    });
                }
            }

            // 6. Keyword search across Name, Username, Mobile, Email, Business Type
            if ($request->filled('search_name') && $request->search_name != 's') {
                $searchTerm = $request->search_name;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('name', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('username', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('mobile', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('email', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('business_name', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('business_type', 'LIKE', "%{$searchTerm}%");
                });
            }

            // 7. State & City filter
            if ($request->filled('state')) {
                $query->where('state', $request->state);
            }

            if ($request->filled('city')) {
                $query->where('city', $request->city);
            }

            $results = $query->limit(50)->get();
        }

        return view('member.search', compact('states', 'cities', 'results', 'hasSearched'));
    }
}
