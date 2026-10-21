<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Affiliate Admin - TIDONG</title><style>body{background:#f5f7fa;color:#17263d;font-family:system-ui,sans-serif;margin:0}.wrap{max-width:960px;margin:24px auto;padding:14px}.panel{background:white;border:1px solid #d9e1eb;padding:20px;margin:16px 0;border-radius:12px}.field{width:100%;max-width:400px;display:block;margin:5px 0 14px;padding:10px;border:1px solid #bcc8d5;border-radius:6px}button{border:0;background:#174eb7;color:#fff;border-radius:7px;padding:10px 15px}.small{color:#576980;font-size:14px}.warn{padding:14px;background:#fff1d8;border-radius:8px}</style></head><body><main class="wrap"><a href="{{ route('home') }}">← Admin Dashboard</a><img src="{{ asset('assets/tidong-official-logo.jpeg') }}" alt="TIDONG official logo" width="150" style="max-width:50%;height:auto"><h1>Affiliate Configuration</h1>
<div class="panel warn">Live money movement is disabled by code. Fee collection, wallets, royalty and payouts have NO enabling route. All 14 old stages are historical references pending legal review.</div>
@if(session('success'))<p class="panel">{{ session('success') }}</p>@endif
@if($errors->any())<div class="panel" role="alert">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
<div class="panel"><h2>First ID Setup</h2>
@if(!$settings->root_member_id)
<p>One-time setup for a verified customer. Must be the very first Affiliate member. Existing V1 members require manual reconciliation.</p>
<form method="post" action="{{ route('affiliate.admin.root') }}">@csrf
<label for="first_customer_user_id">Verified First Customer User ID</label>
<input class="field" type="number" min="1" id="first_customer_user_id" name="first_customer_user_id" required>
<button type="submit">Initialize First ID</button></form>
@else<p>First ID: {{ $settings->root_member_id }} • Current A-chain tail: {{ $settings->a_chain_tail_member_id }}</p>@endif
</div>
<div class="panel"><p><strong>Registered members:</strong> {{ number_format($registrations) }}</p><p><strong>Finance Review:</strong> {{ $settings->financial_review_status }}</p>
<form method="post" action="{{ route('affiliate.admin.settings') }}">@csrf
<label>Allow Enrollment?</label><select class="field" name="registration_enabled"><option value="1" @selected($settings->registration_enabled)>Yes</option><option value="0" @selected(!$settings->registration_enabled)>No</option></select>
<label>Referral Tracking?</label><select class="field" name="referral_tracking_enabled"><option value="1" @selected($settings->referral_tracking_enabled)>Yes</option><option value="0" @selected(!$settings->referral_tracking_enabled)>No</option></select>
<label>Registration Fee (₹) — draft, NOT collected</label><input class="field" type="number" min="0" max="999999" step="0.01" name="registration_fee_rupees" value="{{ number_format($settings->registration_fee_paise / 100, 2, '.', '') }}" required>
<label>Sale Estimate Rate (basis points, 100 = 1%)</label><input class="field" type="number" min="0" max="10000" step="1" name="estimate_rate_bps" value="{{ $settings->estimate_rate_bps }}" required>
<p class="small">Changing this draft rate does not create any payable liability and does not recalculate old snapshots. Verified sale adapter is not yet wired to Master order system.</p>
<button type="submit">Save Draft Settings</button></form></div>
<div class="panel"><h2>14 legacy stages — reference only</h2><p class="small">Targets are from old source; do not advertise, award or pay them.</p>
@foreach($stages as $stage)
<form method="post" action="{{ route('affiliate.admin.stage', ['stage'=>$stage->stage_no]) }}" style="border-top:1px solid #dce4ec;padding:16px 0">@csrf
<strong>Stage {{ $stage->stage_no }}</strong> <span class="small">({{ $stage->status }})</span>
<label>Name</label><input class="field" name="name" maxlength="255" value="{{ $stage->name }}" required>
<label>Historical Leg A count</label><input class="field" type="number" min="0" name="legacy_leg_a_count" value="{{ $stage->legacy_leg_a_count }}" required>
<label>Historical Leg B count</label><input class="field" type="number" min="0" name="legacy_leg_b_count" value="{{ $stage->legacy_leg_b_count }}" required>
<button type="submit">Update Reference</button>
</form>
@endforeach
</div></main></body></html>
