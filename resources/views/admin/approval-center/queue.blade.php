@extends('layouts.admin')
@section('title',ucwords(str_replace('_',' ',$type)).' Reviews')
@section('page_heading',ucwords(str_replace('_',' ',$type)).' Reviews')
@section('content')
<div class="mx-auto max-w-5xl space-y-5"><a href="{{ route('admin.approval-center.index') }}" class="text-teal-800 underline">← All Approval Groups</a>
<h1 class="text-2xl font-bold">{{ ucwords(str_replace('_',' ',$type)) }} Applications</h1>
<p class="rounded-lg border bg-white p-4 text-sm">Order: Verified Email → Complete Profile → Independent Credential Check → Admin Decision → Role-specific access. Approval does not activate payouts, vendor access or hidden services.</p>
@if(session('success'))<p role="status" class="rounded-lg bg-emerald-50 p-3 text-emerald-900">{{ session('success') }}</p>@endif
@if($errors->any())<ul role="alert" class="rounded-lg bg-red-50 p-3 text-red-900">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>@endif
@if(!$enabled)<p class="rounded-lg bg-amber-50 p-3 text-amber-900">Reviews are disabled in Module Management. Read-only inspection remains available.</p>@endif
@forelse($records as $a)
<article class="rounded-xl border bg-white p-5 shadow-sm">
<div class="flex flex-wrap justify-between gap-3"><div><h2 class="text-lg font-bold">{{ $a->name }} · {{ $a->service_title }}</h2><p class="text-sm text-slate-600">{{ $a->email }} · {{ $a->category }}</p></div><span class="font-bold">{{ ucfirst($a->status) }}</span></div>
<div class="mt-4 grid gap-2 text-sm sm:grid-cols-3"><div class="rounded-lg border p-3 {{ $a->email_verified_at?'bg-emerald-50':'bg-amber-50' }}">1. Email: {{ $a->email_verified_at?'Verified':'Unverified' }}</div>
<div class="rounded-lg border bg-emerald-50 p-3">2. Profile: Submitted</div><div class="rounded-lg border p-3">3. Admin: {{ ucfirst($a->status) }}</div></div>
<p class="mt-3 text-sm"><strong>Registration reference:</strong> {{ $a->registration_reference }}</p><p class="mt-2 whitespace-pre-wrap text-sm">{{ $a->experience_description }}</p>
@if($a->status==='pending' && $enabled && $a->email_verified_at)
<form method="POST" action="{{ route('admin.approval-center.decide',['type'=>$type,'user'=>$a->user_id]) }}" class="mt-4 grid gap-3 rounded-lg border p-4">@csrf
<label class="font-semibold">Decision<select name="decision" required class="mt-1 w-full rounded-lg border p-3"><option value="">Select Decision</option><option value="approved">Approve after independent verification</option><option value="rejected">Reject with reason</option></select></label>
<label class="font-semibold">Independent verification record / reference<input name="verification_reference" maxlength="250" class="mt-1 w-full rounded-lg border p-3"></label>
<label class="font-semibold">Reason if rejected<input name="reason" maxlength="500" class="mt-1 w-full rounded-lg border p-3"></label>
<label class="flex items-start gap-3 text-sm"><input type="checkbox" name="confirm_independent_check" value="1" class="mt-1"> I independently checked the submitted details against appropriate evidence.</label>
<button type="submit" class="min-h-12 rounded-lg bg-teal-700 px-5 py-3 font-bold text-white">Record Review Decision</button></form>
@elseif(!$a->email_verified_at && $a->status==='pending')<p class="mt-3 rounded-lg bg-amber-50 p-3 text-sm text-amber-900">Approval locked: applicant must verify email first.</p>
@endif
</article>
@empty <p class="rounded-lg border bg-white p-4">No applications in this group.</p>@endforelse
{{ $records->links() }}
</div>
@endsection