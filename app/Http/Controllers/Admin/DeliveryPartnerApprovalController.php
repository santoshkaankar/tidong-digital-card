<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Delivery\DeliveryIdentity;
use App\Services\Platform\FeatureRegistry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/** Identity approval only; NEVER platform access to vendor dispatch or internal financials. */
final class DeliveryPartnerApprovalController extends Controller
{
    public function index(): View
    {
        $partners = DB::table('delivery_partners as dp')->join('users as u','u.id','=','dp.user_id')
            ->select('dp.id','dp.status','dp.vehicle_type','dp.phone','dp.approved_at','dp.verified_at',
                'u.name','u.email','u.email_verified_at')
            ->orderByRaw("CASE WHEN dp.approved_at IS NULL THEN 0 ELSE 1 END")
            ->orderByDesc('dp.id')->paginate(25);
        return view('admin.delivery.approvals', compact('partners'));
    }

    public function approve(Request $request, int $partner): RedirectResponse
    {
        $this->assertApprovalFeature();
        DB::transaction(function () use ($request, $partner): void {
            $record = DB::table('delivery_partners')->where('id', $partner)->lockForUpdate()->first();
            abort_unless($record, 404);
            if ($record->status !== 'pending' || $record->approved_at) {
                throw ValidationException::withMessages(['partner' => 'This application is not awaiting platform approval.']);
            }
            if (app(DeliveryIdentity::class)->isIneligibleRider((int)$record->user_id)) {
                throw ValidationException::withMessages(['partner' => 'This user is not eligible for delivery work.']);
            }
            $applicant = DB::table('users')->where('id', $record->user_id)->first();
            if (! $applicant || ! $applicant->email_verified_at
                || ! in_array($record->vehicle_type, ['bike','scooter','bicycle','car','van','on_foot'], true)
                || ! $record->phone) {
                throw ValidationException::withMessages(['partner' => 'Verified email and completed delivery profile are required.']);
            }
            DB::table('delivery_partners')->where('id', $partner)->update([
                'approved_by' => $request->user()->id, 'approved_at' => now(),
                'verification_status' => 'verified', 'verified_by' => $request->user()->id,
                'verified_at' => now(), 'updated_at' => now(),
                // Deliberately do not make active or available. Vendor approval is separate.
            ]);
            DB::table('audit_logs')->insert([
                'actor_user_id' => $request->user()->id,
                'action' => 'delivery.partner.platform_approved',
                'entity_type' => 'delivery_partner', 'entity_id' => $partner,
                'new_values' => json_encode(['stage' => 'platform_approved','vendor_approval_required' => true]),
                'created_at' => now(),
            ]);
        });
        return back()->with('success', 'Platform approval recorded. Vendor authorization is still required.');
    }

    public function reject(Request $request, int $partner): RedirectResponse
    {
        $this->assertApprovalFeature();
        $data = $request->validate(['reason' => ['required','string','min:5','max:500']]);
        DB::transaction(function () use ($request, $partner, $data): void {
            $record = DB::table('delivery_partners')->where('id', $partner)->lockForUpdate()->first();
            abort_unless($record, 404);
            if ($record->status !== 'pending' || $record->approved_at) {
                throw ValidationException::withMessages(['partner' => 'Only pending applications can be rejected.']);
            }
            DB::table('delivery_partners')->where('id', $partner)->update([
                'status' => 'rejected', 'is_available' => false,
                'verification_status' => 'rejected', 'updated_at' => now(),
            ]);
            DB::table('audit_logs')->insert([
                'actor_user_id' => $request->user()->id,
                'action' => 'delivery.partner.platform_rejected',
                'entity_type' => 'delivery_partner', 'entity_id' => $partner,
                'new_values' => json_encode(['reason' => $data['reason']]), 'created_at' => now(),
            ]);
        });
        return back()->with('success','Delivery partner application rejected.');
    }

    private function assertApprovalFeature(): void
    {
        abort_unless(app(FeatureRegistry::class)->isEnabled('delivery.partner_approval'), 503,
            'Delivery partner approval feature is disabled.');
    }
}
