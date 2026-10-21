<?php

namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\Controller;
use App\Services\Affiliate\AffiliateMembershipService;
use App\Services\Platform\ModuleRegistry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

final class AffiliateController extends Controller
{
    private function assertModuleEnabled(): void
    {
        abort_unless(app(ModuleRegistry::class)->isEnabled('affiliate'), 404);
    }

    public function referral(Request $request, string $code, string $leg)
    {
        $this->assertModuleEnabled();
        $code = strtoupper($code);
        $leg = strtoupper($leg);
        abort_unless(preg_match('/^[A-F0-9]{16}$/', $code) && in_array($leg, ['A', 'B'], true), 404);
        abort_unless(DB::table('affiliate_members')->where('referral_code', $code)->where('status', 'active')->exists(), 404);
        // Only validated sponsor and leg, never a user_id supplied by the browser.
        $request->session()->put('affiliate_referral_code', $code);
        $request->session()->put('affiliate_referral_leg', $leg);
        return redirect()->route('affiliate.index');
    }

    public function index(Request $request)
    {
        $this->assertModuleEnabled();
        $settings = DB::table('affiliate_settings')->where('id', 1)->first();
        abort_unless($settings, 503);
        $member = DB::table('affiliate_members')->where('user_id', $request->user()->id)->first();
        $count = $member ? DB::table('affiliate_members')->where('referrer_member_id', $member->id)->count() : 0;
        $estimates = $member ? DB::table('affiliate_estimates')->where('member_id', $member->id)
            ->orderByDesc('id')->limit(20)->get() : collect();
        return view('affiliate.index', compact('settings', 'member', 'count', 'estimates'));
    }

    public function enroll(Request $request, AffiliateMembershipService $members)
    {
        $this->assertModuleEnabled();
        $validated = $request->validate([
            'referral_code' => ['nullable', 'string', 'size:16', 'regex:/^[a-fA-F0-9]{16}$/'],
            'leg' => ['nullable', 'in:A,B'],
            'accept_terms' => ['accepted'],
        ]);
        $code = $validated['referral_code'] ?? $request->session()->get('affiliate_referral_code');
        $leg = $validated['leg'] ?? $request->session()->get('affiliate_referral_leg');
        $members->enroll((int) $request->user()->id, $code, $leg);
        $request->session()->forget(['affiliate_referral_code', 'affiliate_referral_leg']);
        return redirect()->route('affiliate.index')->with('success', 'Your free membership has been recorded.');
    }
}
