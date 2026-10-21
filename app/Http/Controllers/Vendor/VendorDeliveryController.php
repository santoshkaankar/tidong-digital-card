<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Services\Delivery\DeliveryService;
use App\Services\Delivery\VendorDeliveryAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/** Vendor business operations; platform identities fail closed in VendorDeliveryAccess. */
class VendorDeliveryController extends Controller
{
    public function index(Request $request, VendorDeliveryAccess $access): View
    {
        $actor = (int) $request->user()->id;
        $allBusinesses = DB::table('businesses as b')
            ->join('vendors as v', 'v.id', '=', 'b.vendor_id')
            ->where('b.status', 'approved')->where('v.status', 'approved')
            ->whereNull('b.deleted_at')->whereNull('v.deleted_at')
            ->orderBy('b.name')->get(['b.id', 'b.name']);
        $allBranches = DB::table('branches')->where('status', 'active')->whereNull('deleted_at')
            ->orderBy('name')->get(['id', 'business_id', 'name']);
        $branches = $allBranches->filter(fn ($branch) => $access->allows(
            $actor, (int)$branch->business_id, (int)$branch->id, 'delivery.jobs.manage'));
        $branchIds = $branches->pluck('id');
        $businessIds = $branches->pluck('business_id')->unique();
        $businesses = $allBusinesses->whereIn('id', $businessIds);
        $managedBusinesses = $allBusinesses->filter(fn ($b) => $access->allows(
            $actor, (int)$b->id, null, 'delivery.partners.manage'));
        $managedBusinessIds = $managedBusinesses->pluck('id');
        abort_if($branchIds->isEmpty() && $managedBusinessIds->isEmpty(), 403,
            'No authorized business delivery access.');

        $partners = $businessIds->isEmpty() ? collect() : DB::table('delivery_business_partners as bp')
            ->join('delivery_partners as p', 'p.id', '=', 'bp.partner_id')
            ->join('users as u', 'u.id', '=', 'p.user_id')
            ->whereIn('bp.business_id', $businessIds)->where('bp.status', 'active')
            ->where('p.status', 'active')->where('p.is_available', true)
            ->get(['bp.business_id','p.id','u.name']);
        $roster = $managedBusinessIds->isEmpty() ? collect() : DB::table('delivery_business_partners as bp')
            ->join('delivery_partners as p', 'p.id', '=', 'bp.partner_id')
            ->join('users as u', 'u.id', '=', 'p.user_id')
            ->whereIn('bp.business_id', $managedBusinessIds)
            ->orderByDesc('bp.id')->get(['bp.business_id','bp.partner_id', 'bp.status',
                'bp.verified_at', 'u.name','u.email']);
        $jobs = DB::table('delivery_jobs as dj')
            ->join('businesses as b', 'b.id', '=', 'dj.business_id')
            ->join('branches as br', 'br.id', '=', 'dj.branch_id')
            ->join('delivery_partners as p', 'p.id', '=', 'dj.partner_id')
            ->join('users as u', 'u.id', '=', 'p.user_id')
            ->whereIn('dj.branch_id', $branchIds)
            ->select('dj.id', 'dj.business_id', 'dj.reference', 'dj.status', 'dj.partner_id',
                'dj.created_at', 'dj.return_confirmed_at', 'dj.delivery_fee_paise',
                'dj.partner_earning_paise', 'b.name as business_name',
                'br.name as branch_name', 'u.name as partner_name')
            ->orderByDesc('dj.id')->paginate(20);
        $employees = $managedBusinessIds->isEmpty() ? collect() : DB::table('employee_assignments as ea')
            ->join('users as u', 'u.id', '=', 'ea.user_id')
            ->whereIn('ea.business_id', $managedBusinessIds)->where('ea.is_active', true)
            ->where('ea.starts_at', '<=', now())
            ->where(fn ($q) => $q->whereNull('ea.ends_at')->orWhere('ea.ends_at', '>', now()))
            ->get(['ea.business_id','ea.branch_id','u.id','u.name']);
        return view('vendor.delivery.index', compact('businesses', 'branches', 'partners',
            'roster', 'jobs', 'managedBusinesses', 'employees'));
    }

    public function invite(Request $request, DeliveryService $delivery): RedirectResponse
    {
        $data = $request->validate([
            'business_id' => ['required','integer','exists:businesses,id'],
            'email' => ['required','email','exists:users,email'],
            'vehicle_type' => ['required', Rule::in(['bike','bicycle','car','van','on_foot'])],
            'phone' => ['nullable','string','max:30'],
        ]);
        $id = (int) DB::table('users')->where('email', $data['email'])->value('id');
        $delivery->inviteToBusiness((int)$data['business_id'], $id, $data['vehicle_type'],
            $data['phone'] ?? null, (int)$request->user()->id);
        return back()->with('success', 'Partner invited for this business. Verification and approval remain with the Vendor.');
    }

    public function verify(Request $request, DeliveryService $delivery, int $business, int $partner): RedirectResponse
    {
        $request->validate(['verification_confirmed' => ['required','accepted']]);
        $delivery->verifyForBusiness($business, $partner, (int)$request->user()->id);
        return back()->with('success', 'Partner verification attested for your business.');
    }

    public function status(Request $request, DeliveryService $delivery, int $business, int $partner): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', Rule::in(['active','suspended'])]]);
        $delivery->setRosterStatus($business, $partner, $data['status'], (int)$request->user()->id);
        return back()->with('success', 'Business partner access updated.');
    }

    public function store(Request $request, DeliveryService $delivery): RedirectResponse
    {
        $data = $request->validate([
            'business_id' => ['required','integer','exists:businesses,id'],
            'branch_id' => ['required','integer','exists:branches,id'],
            'partner_id' => ['required','integer','exists:delivery_partners,id'],
            'pos_order_id' => ['nullable','integer','exists:pos_orders,id'],
            'recipient_name' => ['required','string','max:160'],
            'recipient_phone' => ['required','string','max:30'],
            'dropoff_address' => ['required','string','min:8','max:2000'],
            'delivery_note' => ['nullable','string','max:500'],
            'delivery_fee_paise' => ['nullable','integer','min:0','max:100000000'],
            'partner_earning_paise' => ['nullable','integer','min:0','max:100000000'],
        ]);
        $delivery->createJob($data, (int)$request->user()->id);
        return back()->with('success','Delivery assigned. Give one-time PIN to recipient only.')
            ->with('delivery_pin_once', $delivery->takeHandoffPin());
    }

    public function cancel(Request $request, DeliveryService $delivery, int $job): RedirectResponse
    {
        $delivery->progress($job, (int)$request->user()->id, 'cancelled', false);
        return back()->with('success','Delivery cancelled with retained history.');
    }

    public function confirmReturn(Request $request, DeliveryService $delivery, int $job): RedirectResponse
    {
        $request->validate(['return_confirmed' => ['required','accepted']]);
        $delivery->confirmReturned($job, (int)$request->user()->id);
        return back()->with('success','Parcel return confirmed.');
    }

    public function reassign(Request $request, DeliveryService $delivery, int $job): RedirectResponse
    {
        $data = $request->validate(['partner_id' => ['required','integer','exists:delivery_partners,id']]);
        $delivery->reassign($job, (int)$data['partner_id'], (int)$request->user()->id);
        return back()->with('success','Reassignment recorded. Share the NEW PIN with recipient only.')
            ->with('delivery_pin_once', $delivery->takeHandoffPin());
    }

    public function reissuePin(Request $request, DeliveryService $delivery, int $job): RedirectResponse
    {
        $request->validate(['rotation_confirmed' => ['required', 'accepted']]);
        $delivery->reissuePin($job, (int) $request->user()->id);
        return back()->with('success', 'Recipient confirmation PIN replaced. Old PIN no longer works.')
            ->with('delivery_pin_once', $delivery->takeHandoffPin());
    }

    public function grant(Request $request, VendorDeliveryAccess $access): RedirectResponse
    {
        $data = $request->validate([
            'business_id' => ['required','integer','exists:businesses,id'],
            'employee_id' => ['required','integer','exists:users,id'],
            'branch_id' => ['nullable','integer','exists:branches,id'],
            'permission_code' => ['required', Rule::in(['delivery.jobs.manage','delivery.partners.manage'])],
            'enabled' => ['required','boolean'],
        ]);
        $access->grant((int)$request->user()->id, (int)$data['employee_id'],
            (int)$data['business_id'], isset($data['branch_id']) ? (int)$data['branch_id'] : null,
            $data['permission_code'], (bool)$data['enabled']);
        return back()->with('success','Individual, business-scoped employee permission updated and audited.');
    }
}
