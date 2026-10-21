<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Business Delivery — TIDONG</title>@vite(['resources/css/app.css','resources/js/app.js'])</head>
<body class="min-h-screen bg-slate-100 text-slate-900"><header class="bg-slate-900 p-4 text-white"><div class="mx-auto max-w-6xl flex flex-wrap justify-between gap-3"><strong>Business Delivery Management</strong><a class="underline" href="{{ url('/vendor') }}">Vendor Dashboard</a></div></header>
<main class="mx-auto max-w-6xl space-y-5 p-5">
<p class="text-sm">Company dispatch and partner approvals belong to the firm. Platform operators can switch global availability but cannot operate these forms. Delivery fees and earnings are non-payable snapshots; payouts are not enabled.</p>
@if(session('delivery_pin_once'))<div class="rounded border-2 border-amber-500 bg-amber-50 p-4" role="alert"><strong>Recipient PIN: {{ session('delivery_pin_once') }}</strong><p>Share privately with the recipient, never the partner. Shown once.</p></div>@endif
@if(session('success'))<p class="rounded bg-green-50 p-3">{{ session('success') }}</p>@endif
@if($errors->any())<div class="rounded bg-red-50 p-3"><ul>@foreach($errors->all() as $message)<li>{{ $message }}</li>@endforeach</ul></div>@endif
@if($managedBusinesses->isNotEmpty())
<section class="rounded bg-white p-4 shadow space-y-3"><h2 class="text-xl font-semibold">Invite Existing Authorized Partner Account</h2>
<p class="text-sm">Public registration stays closed. Only existing test or authorized accounts can be invited.</p>
<form method="POST" action="{{ route('vendor.delivery.partners.invite') }}" class="grid gap-3 md:grid-cols-2">@csrf
<label>Business<select name="business_id" required class="block w-full border p-2"><option value="">Choose business</option>@foreach($managedBusinesses as $b)<option value="{{ $b->id }}">{{ $b->name }}</option>@endforeach</select></label>
<label>Account Email<input type="email" name="email" required class="block w-full border p-2"></label>
<label>Vehicle<select name="vehicle_type" class="block w-full border p-2"><option value="bike">Bike</option><option value="bicycle">Bicycle</option><option value="car">Car</option><option value="van">Van</option><option value="on_foot">On Foot</option></select></label>
<label>Phone (optional)<input name="phone" class="block w-full border p-2" maxlength="30"></label><button type="submit" class="rounded bg-blue-700 p-2 text-white">Invite Partner</button></form></section>
<section class="rounded bg-white p-4 shadow space-y-3"><h2 class="text-xl font-semibold">Business Partner Verification &amp; Approval</h2>
@forelse($roster as $r)<div class="flex flex-wrap items-center justify-between gap-2 border-b py-3"><div><strong>{{ $r->name }}</strong> — {{ $r->email }}<div class="text-sm">Business #{{ $r->business_id }} | {{ ucfirst($r->status) }} | {{ $r->verified_at ? 'Screened' : 'Not screened' }}</div></div><div class="flex flex-wrap gap-2">
@if(!$r->verified_at)<form method="POST" action="{{ route('vendor.delivery.partners.verify',[$r->business_id,$r->partner_id]) }}">@csrf<label><input type="checkbox" name="verification_confirmed" value="1" required> Screening completed</label><button class="rounded border p-2" type="submit">Attest</button></form>@endif
@if($r->verified_at)<form method="POST" action="{{ route('vendor.delivery.partners.status',[$r->business_id,$r->partner_id]) }}">@csrf<input type="hidden" name="status" value="{{ $r->status === 'active' ? 'suspended' : 'active' }}"><button class="rounded bg-slate-900 px-3 py-2 text-white" type="submit">{{ $r->status === 'active' ? 'Suspend for Firm' : 'Approve for Firm' }}</button></form>@endif</div></div>
@empty<p class="text-sm text-slate-500">No partner invitations.</p>@endforelse</section>
<section class="rounded bg-white p-4 shadow space-y-3"><h2 class="text-xl font-semibold">Delegate Delivery Permissions to Firm Employees</h2><p class="text-sm">Only Firm owner can approve a delegated permission. Employee must already have an active business/branch employee assignment.</p>
<form method="POST" action="{{ route('vendor.delivery.permissions.update') }}" class="grid gap-3 md:grid-cols-2">@csrf
<label>Business<select name="business_id" class="block w-full border p-2" required><option value="">Choose business</option>@foreach($managedBusinesses as $b)<option value="{{ $b->id }}">{{ $b->name }}</option>@endforeach</select></label>
<label>Employee<select name="employee_id" class="block w-full border p-2" required><option value="">Choose employee</option>@foreach($employees as $e)<option value="{{ $e->id }}">{{ $e->name }} (Business #{{ $e->business_id }}{{ $e->branch_id ? ', Branch #'.$e->branch_id : '' }})</option>@endforeach</select></label>
<label>Branch (leave empty for business-wide)<select name="branch_id" class="block w-full border p-2"><option value="">Whole Business</option>@foreach($branches as $b)<option value="{{ $b->id }}">{{ $b->name }} (Business #{{ $b->business_id }})</option>@endforeach</select></label>
<label>Permission<select name="permission_code" class="block w-full border p-2"><option value="delivery.jobs.manage">Delivery Dispatch &amp; Returns</option><option value="delivery.partners.manage">Partner Management</option></select></label>
<label>Change<select name="enabled" class="block w-full border p-2"><option value="1">Grant</option><option value="0">Revoke</option></select></label><button class="rounded bg-slate-900 p-2 text-white" type="submit">Update Permission</button></form></section>
@endif
@if($branches->isNotEmpty())
<section class="rounded bg-white p-4 shadow space-y-3"><h2 class="text-xl font-semibold">Assign Delivery</h2><form method="POST" action="{{ route('vendor.delivery.jobs.store') }}" class="grid gap-3 md:grid-cols-2">@csrf
<label>Business<select name="business_id" class="block w-full border p-2" required><option value="">Choose</option>@foreach($businesses as $b)<option value="{{ $b->id }}">{{ $b->name }}</option>@endforeach</select></label>
<label>Branch<select name="branch_id" class="block w-full border p-2" required><option value="">Choose matching branch</option>@foreach($branches as $b)<option value="{{ $b->id }}">{{ $b->name }} (Business #{{ $b->business_id }})</option>@endforeach</select></label>
<label>Approved Available Partner<select name="partner_id" class="block w-full border p-2" required><option value="">Choose matching business partner</option>@foreach($partners as $p)<option value="{{ $p->id }}">{{ $p->name }} (Business #{{ $p->business_id }})</option>@endforeach</select></label>
<label>POS Order ID (optional)<input type="number" min="1" name="pos_order_id" class="block w-full border p-2"></label>
<label>Recipient Name<input name="recipient_name" maxlength="160" required class="block w-full border p-2"></label>
<label>Recipient Phone<input name="recipient_phone" maxlength="30" required class="block w-full border p-2"></label>
<label class="md:col-span-2">Drop-off Address<textarea name="dropoff_address" maxlength="2000" minlength="8" required class="block w-full border p-2"></textarea></label>
<label>Delivery Fee (paise)<input type="number" name="delivery_fee_paise" min="0" value="0" class="block w-full border p-2"></label>
<label>Partner Earning Snapshot (paise)<input type="number" name="partner_earning_paise" min="0" value="0" class="block w-full border p-2"></label>
<label>Note<input name="delivery_note" maxlength="500" class="block w-full border p-2"></label><button class="rounded bg-blue-700 p-2 text-white" type="submit">Assign Delivery</button></form></section>
@endif
<section class="rounded bg-white p-4 shadow space-y-3"><h2 class="text-xl font-semibold">Authorized Business Deliveries</h2><div class="overflow-x-auto"><table class="min-w-[820px] w-full text-left text-sm"><thead><tr class="border-b"><th class="p-2">Reference</th><th class="p-2">Business / Branch</th><th class="p-2">Partner</th><th class="p-2">Status</th><th class="p-2">Actions</th></tr></thead><tbody>
@forelse($jobs as $j)<tr class="border-b"><td class="p-2">{{ $j->reference }}</td><td class="p-2">{{ $j->business_name }} / {{ $j->branch_name }}</td><td class="p-2">{{ $j->partner_name }}</td><td class="p-2">{{ ucfirst(str_replace('_',' ',$j->status)) }}</td><td class="p-2 space-y-2">
@if(in_array($j->status,['assigned','accepted','picked_up']))
<form method="POST" action="{{ route('vendor.delivery.jobs.reissue-pin',$j->id) }}">@csrf
<label class="text-xs"><input type="checkbox" name="rotation_confirmed" value="1" required> Replace lost/locked PIN (old PIN invalidated)</label>
<button class="rounded border border-amber-600 px-2 py-1" type="submit">Reissue Recipient PIN</button></form>
@endif
@if(in_array($j->status,['assigned','accepted','declined','failed']))<form method="POST" action="{{ route('vendor.delivery.jobs.cancel',$j->id) }}">@csrf<button class="rounded border border-red-600 px-2 py-1" type="submit">Cancel</button></form>@endif
@if($j->status === 'failed' && !$j->return_confirmed_at)<form method="POST" action="{{ route('vendor.delivery.jobs.return',$j->id) }}">@csrf<label><input type="checkbox" name="return_confirmed" value="1" required> Physical return received</label><button class="rounded border px-2" type="submit">Confirm Return</button></form>@endif
@if(in_array($j->status,['assigned','declined']) || ($j->status === 'failed' && $j->return_confirmed_at))<form method="POST" action="{{ route('vendor.delivery.jobs.reassign',$j->id) }}">@csrf<select name="partner_id" required class="border p-1"><option value="">Approved replacement</option>@foreach($partners->where('business_id',$j->business_id) as $p)@if($p->id !== $j->partner_id)<option value="{{ $p->id }}">{{ $p->name }}</option>@endif @endforeach</select><button class="rounded border px-2" type="submit">Reassign</button></form>@endif
</td></tr>@empty<tr><td class="p-3" colspan="5">No deliveries for your authorized branches.</td></tr>@endforelse
</tbody></table></div>{{ $jobs->links() }}</section>
</main></body></html>
