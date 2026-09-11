<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\Restaurant\WaiterCall;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WaiterCallController extends Controller
{
    /**
     * Resolve Waiter Call (Delete Record)
     */
    public function resolve($id)
    {
        $call = WaiterCall::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if ($call) {
            $call->delete(); // Database se record remove karein

            return response()->json([
                'success' => true,
                'message' => 'Waiter call resolved successfully'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Waiter call not found'
        ], 404);
    }
}