<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use App\Services\Delivery\DeliveryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DeliveryPartnerController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless(app(\App\Services\Platform\ModuleRegistry::class)->isEnabled('delivery'),503);
        $partner = DB::table('delivery_partners')->where('user_id',$request->user()->id)
            ->where('status','active')->first();
        abort_unless($partner,403);
        $jobs = DB::table('delivery_jobs as dj')
            ->join('branches as br','br.id','=','dj.branch_id')
            ->where('dj.partner_id',$partner->id)
            ->select('dj.*','br.name as branch_name')->orderByDesc('dj.id')->paginate(20);
        return view('delivery.dashboard',compact('jobs','partner'));
    }

    public function availability(Request $request, DeliveryService $delivery): RedirectResponse
    {
        $data = $request->validate(['available' => ['required','boolean']]);
        $delivery->setAvailability((int)$request->user()->id, (bool)$data['available']);
        return back()->with('success', 'Availability updated.');
    }

    public function update(Request $request,DeliveryService $delivery,int $job): RedirectResponse
    {
        $data = $request->validate([
            'status'=>['required','in:accepted,declined,picked_up,delivered,failed'],
            'handoff_pin'=>['required_if:status,delivered','nullable','digits:6'],
            'recipient_name'=>['required_if:status,delivered','nullable','string','max:160'],
            'failure_reason'=>['required_if:status,failed','nullable','string','max:500'],
        ]);
        $delivery->progress($job,(int)$request->user()->id,$data['status'],true,$data);
        return back()->with('success','Delivery status updated.');
    }
}
