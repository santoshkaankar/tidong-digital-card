<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\Customer\CustomerMarketplace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CustomerDashboardController extends Controller
{
    public function __invoke(Request $request, CustomerMarketplace $marketplace)
    {
        $user = $request->user();

        $hasOrderTable = Schema::hasTable('sublimation_orders')
            && Schema::hasColumn('sublimation_orders', 'customer_user_id');
        $hasGroceryOrders = Schema::hasTable('grocery_online_orders')
            && Schema::hasColumn('grocery_online_orders', 'customer_user_id');
        $orderCount = $hasOrderTable ? DB::table('sublimation_orders')
            ->where('customer_user_id', $user->id)->count() : 0;
        if ($hasGroceryOrders) {
            $orderCount += DB::table('grocery_online_orders')
                ->where('customer_user_id', $user->id)->count();
        }

        return view('customer.dashboard', [
            'hasVendor' => $user->vendor()->exists(),
            'addressCount' => Schema::hasTable('customer_addresses')
                ? $user->customerAddresses()->count() : 0,
            'orderCount' => $orderCount,
            'hasCustomerOrderLink' => $hasOrderTable || $hasGroceryOrders,
            'categories' => $marketplace->categories(),
        ]);
    }
}
