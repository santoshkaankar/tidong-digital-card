<?php

namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\Controller;
use App\Services\Platform\ModuleRegistry;
use App\Services\Affiliate\AffiliateMembershipService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/** Routes MUST remain behind EnsureSuperAdmin (not platform staff or vendors). */
final class AffiliateAdminController extends Controller
{
    public function index()
    {
        abort_unless(app(ModuleRegistry::class)->isEnabled('affiliate'), 404);
        return view('affiliate.admin', [
            'settings' => DB::table('affiliate_settings')->where('id', 1)->first(),
            'stages' => DB::table('affiliate_stage_references')->orderBy('stage_no')->get(),
            'registrations' => DB::table('affiliate_members')->count(),
        ]);
    }

    public function initializeRoot(Request $request, AffiliateMembershipService $members)
    {
        abort_unless(app(ModuleRegistry::class)->isEnabled('affiliate'), 404);
        $data = $request->validate(['first_customer_user_id' => ['required', 'integer', 'min:1']]);
        $members->initializeRoot((int) $data['first_customer_user_id'], (int) $request->user()->id);
        return redirect()->route('affiliate.admin.index')->with('success', 'First ID initialized. Registration fee and payouts remain disabled.');
    }

    public function update(Request $request)
    {
        abort_unless(app(ModuleRegistry::class)->isEnabled('affiliate'), 404);
        $data = $request->validate([
            'registration_enabled' => ['required', 'boolean'],
            'referral_tracking_enabled' => ['required', 'boolean'],
            'registration_fee_rupees' => ['required', 'regex:/^(?:0|[1-9][0-9]{0,5})(?:\.[0-9]{1,2})?$/'],
            'estimate_rate_bps' => ['required', 'integer', 'between:0,10000'],
        ]);
        $parts = explode('.', (string) $data['registration_fee_rupees']);
        $feePaise = ((int) $parts[0] * 100) + (int) str_pad($parts[1] ?? '', 2, '0');

        DB::transaction(function () use ($request, $data, $feePaise): void {
            $old = DB::table('affiliate_settings')->where('id', 1)->lockForUpdate()->first();
            abort_unless($old, 503);
            $update = [
                'registration_enabled' => (bool) $data['registration_enabled'],
                'referral_tracking_enabled' => (bool) $data['referral_tracking_enabled'],
                'registration_fee_paise' => $feePaise, // DRAFT ONLY. No collection endpoint is implemented.
                'estimate_rate_bps' => (int) $data['estimate_rate_bps'],
                'policy_version' => $old->policy_version + 1,
                'fee_collection_enabled' => false,
                'wallet_credit_enabled' => false,
                'withdrawals_enabled' => false,
                'payouts_enabled' => false,
                'financial_review_status' => 'pending',
                'updated_by' => $request->user()->id, 'updated_at' => now(),
            ];
            DB::table('affiliate_settings')->where('id', 1)->update($update);
            DB::table('affiliate_audit_events')->insert([
                'actor_user_id' => $request->user()->id, 'member_id' => null,
                'action' => 'affiliate.settings_changed',
                'metadata' => json_encode([
                    'old' => ['registration_enabled' => $old->registration_enabled, 'referral_tracking_enabled' => $old->referral_tracking_enabled, 'registration_fee_paise' => $old->registration_fee_paise, 'estimate_rate_bps' => $old->estimate_rate_bps],
                    'new' => ['registration_enabled' => $update['registration_enabled'], 'referral_tracking_enabled' => $update['referral_tracking_enabled'], 'registration_fee_paise' => $feePaise, 'estimate_rate_bps' => $update['estimate_rate_bps']],
                ]),
                'created_at' => now(),
            ]);
        });
        return redirect()->route('affiliate.admin.index')->with('success', 'Draft settings saved. Payouts and fee collection remain disabled.');
    }

    public function updateStage(Request $request, int $stage)
    {
        abort_unless(app(ModuleRegistry::class)->isEnabled('affiliate'), 404);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'legacy_leg_a_count' => ['required', 'integer', 'min:0'],
            'legacy_leg_b_count' => ['required', 'integer', 'min:0'],
        ]);
        DB::transaction(function () use ($request, $stage, $data): void {
            $old = DB::table('affiliate_stage_references')->where('stage_no', $stage)->lockForUpdate()->first();
            abort_unless($old, 404);
            DB::table('affiliate_stage_references')->where('id', $old->id)->update($data + ['status' => 'reference_only', 'updated_at' => now()]);
            DB::table('affiliate_audit_events')->insert([
                'actor_user_id' => $request->user()->id, 'member_id' => null,
                'action' => 'affiliate.stage_reference_changed',
                'metadata' => json_encode(['stage_no' => $stage, 'previous_name' => $old->name, 'previous_a' => $old->legacy_leg_a_count, 'previous_b' => $old->legacy_leg_b_count, 'new_values' => $data]),
                'created_at' => now(),
            ]);
        });
        return redirect()->route('affiliate.admin.index')->with('success', 'Historical reference updated; no stage awards activated.');
    }
}
