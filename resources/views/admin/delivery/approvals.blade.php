@extends(request()->routeIs('admin.*') ? 'layouts.admin' : 'layouts.platform-ops')
@section('title','Delivery Partner Approval')
@section('page_heading','Delivery Partner Approval')
@section('content')
<div class="mx-auto max-w-6xl space-y-5">
<h1 class="text-xl font-bold">Delivery Partner Platform Approval</h1>
<p class="rounded-xl border border-amber-200 bg-amber-50 p-3 text-sm">Only verified profiles can be approved. Platform approval does not authorize any vendor or auto-accept jobs. A business must approve its own roster, and the rider must manually come online and accept a job.</p>
@if(session('success'))<div class="rounded-lg bg-green-50 p-3">{{ session('success') }}</div>@endif
@if($errors->any())<div class="rounded-lg bg-red-50 p-3 text-red-800">{{ $errors->first() }}</div>@endif
<div class="grid gap-3">
@forelse($partners as $p)
<article class="rounded-xl border bg-white p-4 shadow-sm">
<div class="flex flex-wrap items-center justify-between gap-2"><div><h2 class="font-semibold">{{ $p->name }} <span class="text-sm text-slate-500">#{{ $p->id }}</span></h2><p class="text-sm text-slate-600">{{ $p->email }}</p></div><span class="rounded-full border px-3 py-1 text-sm">{{ $p->approved_at ? 'Platform approved' : ucfirst($p->status) }}</span></div>
<p class="mt-2 text-sm">Email: {{ $p->email_verified_at ? 'Verified' : 'Not verified' }} · Vehicle: {{ $p->vehicle_type }} · Phone: {{ $p->phone ?: 'Missing' }}</p>
@if(! $p->approved_at && $p->status==='pending')
<div class="mt-3 flex flex-wrap items-end gap-3">
<form method="POST" action="{{ route(explode('.',request()->route()->getName())[0].'.delivery-partner-approvals.approve',$p->id) }}">@csrf
<button type="submit" class="rounded-lg bg-emerald-700 px-4 py-2 text-white disabled:opacity-50" @disabled(! $p->email_verified_at || ! $p->phone || in_array($p->vehicle_type,['','pending']))>Approve Profile</button></form>
@if($p->status==='pending')<form method="POST" action="{{ route(explode('.',request()->route()->getName())[0].'.delivery-partner-approvals.reject',$p->id) }}" class="flex flex-wrap gap-2">@csrf
<label class="text-sm">Reason <input name="reason" minlength="5" maxlength="500" required class="rounded-lg border px-3 py-2" placeholder="Reason for rejection"></label><button class="rounded-lg border border-red-700 px-3 py-2 text-red-800">Reject</button></form>@endif
</div>@endif
</article>
@empty<p class="rounded-lg bg-white p-4">No delivery profiles.</p>@endforelse
</div>{{ $partners->links() }}
</div>
@endsection
