<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\Restaurant\WaiterCall;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderCashController extends Controller
{
    /**
     * Resolve Cash Payment Call
     */
    public function resolve($id)
    {
        try {
            $call = WaiterCall::where('id', $id)
                ->where('user_id', Auth::id())
                ->first();

            if ($call) {
                $call->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Cash request resolved successfully'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Cash request not found'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server Error: ' . $e->getMessage()
            ], 500);
        }
    }
}