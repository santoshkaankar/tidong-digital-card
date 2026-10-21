<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/** Platform technical diagnostics ONLY; never exposes firm dispatch or customer PII. */
class DeliveryManagementController extends Controller
{
    public function index(): View
    {
        $counts = DB::table('delivery_jobs')->select('status')
            ->selectRaw('COUNT(*) as total')->groupBy('status')->orderBy('status')->get();
        $partnerCounts = DB::table('delivery_partners')->select('status')
            ->selectRaw('COUNT(*) as total')->groupBy('status')->orderBy('status')->get();
        return view('admin.delivery.index', compact('counts', 'partnerCounts'));
    }
}
