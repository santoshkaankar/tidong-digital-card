<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\Customer\CustomerMarketplace;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerExploreController extends Controller
{
    public function index(CustomerMarketplace $marketplace): View
    {
        return view('customer.explore', [
            'categories' => $marketplace->categories(),
        ]);
    }

    public function service(Request $request, string $service, CustomerMarketplace $marketplace): View
    {
        $definition = $marketplace->validatedService($service);
        abort_if($definition === null, 404);

        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:70'],
        ]);
        $term = trim($validated['q'] ?? '');

        return view('customer.directory', [
            'service' => $service,
            'definition' => $definition,
            'term' => $term,
            'businesses' => $marketplace->directory($service, $term),
        ]);
    }
}
