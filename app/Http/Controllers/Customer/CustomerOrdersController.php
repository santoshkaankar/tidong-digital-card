<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class CustomerOrdersController extends Controller
{
    public function __invoke(): View
    {
        // Deliberately do not query POS, QR guest or internal studio orders.
        // Those schemas currently lack a verified customer_user_id binding.
        return view('customer.orders');
    }
}
