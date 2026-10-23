@extends(request()->segment(1) === 'admin' ? 'layouts.admin' : 'layouts.platform-ops')
@section('title', 'Review Branch Request')
@section('page_heading', 'Review Branch Request')
@section('content')
<div class="mx-auto max-w-5xl space-y-5">
@php
    $dashboardRoute = str_starts_with($baseName, 'admin.') ? 'home'
        : (str_starts_with($baseName, 'staff-admin.') ? 'staff-admin.home' : 'employee.home');
@endphp
<nav class="flex flex-wrap gap-2" aria-label="Review navigation">
    <a href="{{ route($baseName.'index') }}" class="rounded-lg bg-blue-700 px-4 py-2 font-semibold text-white hover:bg-blue-800">← Back to Approvals</a>
    <a href="{{ route($dashboardRoute) }}" class="rounded-lg border border-slate-300 bg-white px-4 py-2 font-semibold text-blue-700 hover:bg-slate-50">Back to Dashboard</a>
</nav>
<header class="rounded-xl bg-white p-6 shadow-sm"><h1 class="text-2xl font-bold">Review Request #{{ $record->id }}</h1>
<p class="text-slate-600">{{ $record->vendor->display_name }} / {{ $record->branch->business->name }} / {{ $record->branch->name }}</p>
<p>Type: <strong>{{ str_replace('_',' ', $record->change_type) }}</strong> · Status: <strong>{{ ucfirst($record->status) }}</strong></p></header>
@if(session('success'))<p class="rounded-lg bg-green-50 p-4">{{ session('success') }}</p>@endif
@if($errors->any())<div class="rounded-lg bg-red-50 p-4">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
<section class="overflow-x-auto rounded-xl bg-white p-6 shadow-sm"><h2 class="text-lg font-semibold">Old vs Requested Values</h2>
<table class="mt-3 w-full text-left text-sm"><thead><tr><th class="border-b p-3">Field</th><th class="border-b p-3">Currently Saved</th><th class="border-b p-3">Requested</th></tr></thead><tbody>
@foreach($record->proposed_values as $field => $value)
<tr><td class="border-b p-3 font-semibold">{{ str_replace('_',' ', $field) }}</td><td class="border-b p-3">{{ $record->old_values[$field] ?? '—' }}</td><td class="border-b p-3">{{ is_scalar($value) ? $value : json_encode($value) }}</td></tr>
@endforeach</tbody></table>
<p class="mt-4 text-sm text-amber-800">Check the Branch location and tax/licence documents before approving. Platform approval does not constitute official government verification.</p>
</section>
@if($record->status==='pending')
<section class="grid gap-4 rounded-xl bg-white p-6 shadow-sm md:grid-cols-2">
<form method="POST" action="{{ route($baseName.'approve',$record->id) }}">@csrf
<label class="block text-sm font-medium">Approval Note (optional)</label><textarea name="reason" maxlength="1000" rows="3" class="mt-2 w-full rounded-lg border p-3"></textarea>
<button onclick="return confirm('Apply this change to the live Branch/Licence?')" class="mt-3 rounded-lg bg-green-700 px-5 py-3 text-white">Approve and Apply</button></form>
<form method="POST" action="{{ route($baseName.'reject',$record->id) }}">@csrf
<label class="block text-sm font-medium">Rejection Reason (required)</label><textarea name="reason" required minlength="5" maxlength="1000" rows="3" class="mt-2 w-full rounded-lg border p-3"></textarea>
<button onclick="return confirm('Reject this request without applying changes?')" class="mt-3 rounded-lg bg-red-700 px-5 py-3 text-white">Reject Request</button></form>
</section>
@else <p class="rounded-xl bg-white p-4">Reviewed: {{ $record->reviewed_at?->format('d M Y H:i') ?? '—' }} · {{ $record->review_reason }}</p> @endif
</div>
@endsection
