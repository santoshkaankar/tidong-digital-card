<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class CustomerOrdersController extends Controller
{
    public function __invoke(Request $request): View
    {
        // Never mix in POS or anonymous QR transactions: only verified ownership is shown.
        $orders = null;
        if (Schema::hasTable('sublimation_orders') && Schema::hasColumn('sublimation_orders', 'customer_user_id')) {
            $orders = DB::table('sublimation_orders')
                ->where('customer_user_id', (int) $request->user()->id)
                ->orderByDesc('id')->paginate(15);
        }

        return view('customer.orders', compact('orders'));
    }
}
