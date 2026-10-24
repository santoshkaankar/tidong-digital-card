<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

/** Read-only, account-owned order history. No payment or inventory transitions. */
class CustomerOrdersController extends Controller
{
    public function __invoke(Request $request): View
    {
        $customerId = (int) $request->user()->id;
        $requestedView = $request->query('view', 'all');
        $view = is_string($requestedView) && in_array($requestedView, ['all', 'active', 'completed', 'cancelled'], true)
            ? $requestedView : 'all';
        $rawSearch = $request->query('q', '');
        $search = is_string($rawSearch) ? substr(trim($rawSearch), 0, 60) : '';
        $search = preg_replace('/[\x00-\x1F\x7F]/', '', $search) ?? '';

        $orders = null;
        $groceryOrders = null;

        if (Schema::hasTable('sublimation_orders') && Schema::hasColumn('sublimation_orders', 'customer_user_id')) {
            $query = DB::table('sublimation_orders')->where('customer_user_id', $customerId);
            if ($search !== '') {
                if (Schema::hasColumn('sublimation_orders', 'order_no')) {
                    $query->where('order_no', 'like', '%'.$search.'%');
                } else {
                    $query->whereRaw('1 = 0');
                }
            }
            $this->applyStatusFilter($query, $view);
            $orders = $query->orderByDesc('id')
                ->paginate(10, ['*'], 'gift_page')->withQueryString();
        }

        if (Schema::hasTable('grocery_online_orders')
            && Schema::hasColumn('grocery_online_orders', 'customer_user_id')) {
            $query = DB::table('grocery_online_orders')->where('customer_user_id', $customerId);
            if ($search !== '') {
                if (Schema::hasColumn('grocery_online_orders', 'order_number')) {
                    $query->where('order_number', 'like', '%'.$search.'%');
                } else {
                    $query->whereRaw('1 = 0');
                }
            }
            $this->applyStatusFilter($query, $view);
            $groceryOrders = $query->orderByDesc('id')
                ->paginate(10, ['*'], 'grocery_page')->withQueryString();
        }

        return view('customer.orders', compact('orders', 'groceryOrders', 'view', 'search'));
    }

    private function applyStatusFilter(\Illuminate\Database\Query\Builder $query, string $view): void
    {
        $finished = ['completed', 'delivered', 'fulfilled'];
        $cancelled = ['cancelled', 'rejected'];
        if ($view === 'active') {
            $query->whereNotIn('status', array_merge($finished, $cancelled));
        } elseif ($view === 'completed') {
            $query->whereIn('status', $finished);
        } elseif ($view === 'cancelled') {
            $query->whereIn('status', $cancelled);
        }
    }
}
