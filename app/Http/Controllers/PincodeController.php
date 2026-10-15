<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PincodeController extends Controller
{
    /**
     * Safe AJAX Live Search returning office_name, pincode, district, and state
     * Supports City, Area, and Pincode search for MySQL and PostgreSQL.
     */
    public function search(Request $request)
    {
        $query = trim($request->get('q', ''));

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        try {
            $driver = DB::connection()->getDriverName();

            if ($driver === 'pgsql') {
                // Supabase PostgreSQL Engine
                $pincodes = DB::table('pincodes')
                    ->select('id', 'office_name', 'pincode', 'district', 'state_name as state')
                    ->where(function($q) use ($query) {
                        $q->whereRaw("pincode::text LIKE ?", [$query . '%'])
                          ->orWhereRaw("office_name ILIKE ?", ['%' . $query . '%'])
                          ->orWhereRaw("district ILIKE ?", ['%' . $query . '%']);
                    })
                    ->limit(15)
                    ->get();
            } else {
                // Local MySQL Engine
                $pincodes = DB::table('pincodes')
                    ->select('id', 'office_name', 'pincode', 'district', 'state_name as state')
                    ->where(function($q) use ($query) {
                        $q->where('pincode', 'LIKE', $query . '%')
                          ->orWhere('office_name', 'LIKE', '%' . $query . '%')
                          ->orWhere('district', 'LIKE', '%' . $query . '%');
                    })
                    ->limit(15)
                    ->get();
            }

            return response()->json($pincodes);

        } catch (\Exception $e) {
            return response()->json([]);
        }
    }
}