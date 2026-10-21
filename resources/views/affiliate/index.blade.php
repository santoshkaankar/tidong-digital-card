<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>TIDONG Network</title>
<style>body{margin:0;background:#f6f8fb;color:#15243b;font-family:system-ui,sans-serif}.wrap{max-width:800px;margin:24px auto;padding:18px}.panel{background:white;padding:24px;border:1px solid #dae2eb;border-radius:16px;margin-bottom:18px}.btn{background:#254fb9;color:white;border:0;padding:12px 18px;border-radius:9px;cursor:pointer}.field{padding:12px;border:1px solid #b5c2d4;border-radius:8px;width:100%;box-sizing:border-box}.small{color:#56667d;font-size:14px}.alert{background:#fff3da;padding:14px;border-radius:9px}a{color:#204cab}.grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:12px}</style>
</head><body><main class="wrap"><p><a href="{{ route('customer.home') }}">← Customer Dashboard</a></p>
<img src="{{ asset('assets/tidong-official-logo.jpeg') }}" alt="TIDONG official logo" width="160" style="max-width:50%;height:auto"><h1>TIDONG Network</h1>
<div class="panel alert"><strong>Registration is free today.</strong> Payment, withdrawal, T-Coin credit and royalty are on hold. Estimates are not earned or payable money. Historical 14-stage rules remain under legal review.</div>
@if(session('success'))<div class="panel">{{ session('success') }}</div>@endif
@if($errors->any())<div class="panel" role="alert">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
@if(!$member)
<div class="panel"><h2>Join the affiliate program</h2>
@if($settings->registration_enabled)
<p>Every verified TIDONG customer will receive a free network membership at registration after the new customer signup integration is approved. This secure fallback is for already registered customers.</p>
<form action="{{ route('affiliate.enroll') }}" method="post">@csrf
<label for="referral_code">Referral code (optional)</label>
<input class="field" id="referral_code" name="referral_code" value="{{ old('referral_code', session('affiliate_referral_code')) }}" maxlength="16" autocomplete="off">
<label for="leg">Referral placement (only with a referral code)</label>
<select class="field" id="leg" name="leg"><option value="">No referral</option><option value="A" @selected(old('leg', session('affiliate_referral_leg')) === 'A')>Leg A</option><option value="B" @selected(old('leg', session('affiliate_referral_leg')) === 'B')>Leg B</option></select>
<p><label><input type="checkbox" name="accept_terms" value="1" required> I understand this is a registration and referral tracking service only; payouts and earnings are not promised.</label></p>
<button class="btn" type="submit">Join for Free</button></form>
@else<p>New registrations are temporarily paused by platform administration.</p>@endif
</div>
@else
<div class="panel"><h2>Your membership</h2><div class="grid"><div><p class="small">Status</p><strong>{{ ucfirst($member->status) }}</strong></div><div><p class="small">Direct registered referrals</p><strong>{{ number_format($count) }}</strong></div></div>
<p class="small">Choose which leg to invite a customer into:</p>
<p class="small">Leg A: <a href="{{ route('affiliate.referral', ['code' => $member->referral_code, 'leg' => 'A']) }}">Copy Leg A referral link</a></p>
<p class="small">Leg B: <a href="{{ route('affiliate.referral', ['code' => $member->referral_code, 'leg' => 'B']) }}">Copy Leg B referral link</a></p>
<p class="small">Placement parent ID: {{ $member->placement_parent_member_id ?? 'First ID' }} • Leg: {{ $member->placement_leg ?? 'Root' }}</p>
<p class="small">Registration records do not automatically create commission.</p></div>
<div class="panel"><h2>Provisional sale-based estimates</h2>
<p class="small">Only verified, eligible sale events connected by the reviewed order adapter can produce estimates. These are not wallet balances, tax invoices, accrued entitlements or withdrawal rights.</p>
@if($estimates->isEmpty())<p>No eligible sale estimates have been recorded.</p>
@else<ul>@foreach($estimates as $estimate)<li>Verified source {{ $estimate->source_reference }}: ₹{{ number_format($estimate->estimated_commission_paise / 100, 2) }} — estimate only</li>@endforeach</ul>@endif
</div>
@endif
</main></body></html>
