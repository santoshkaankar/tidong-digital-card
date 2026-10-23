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
@php
    // Match the backend approval prerequisites. This is visual guidance, NOT authorization.
    // The controller checks them again under a transaction lock on POST.
    $approvedVehicleTypes = ['bike', 'scooter', 'bicycle', 'car', 'van', 'on_foot'];
    $approvalBlockers = [];
    if (! $approvalEnabled) $approvalBlockers[] = 'Delivery Partner Approval is OFF in Admin Feature Controls.';
    if (! $p->email_verified_at) $approvalBlockers[] = 'Email verification required. Ask the partner to verify their email, or log in and resend the verification link from Registration Status.';
    if (! $p->phone) $approvalBlockers[] = 'Mobile number missing. Ask the partner to complete their Delivery Profile.';
    if (! in_array($p->vehicle_type, $approvedVehicleTypes, true)) $approvalBlockers[] = 'Vehicle type missing or invalid. Ask the partner to update their Delivery Profile.';
    $readyForApproval = count($approvalBlockers) === 0;
@endphp
<div class="mt-3 rounded-xl border p-3 {{ $readyForApproval ? 'border-emerald-300 bg-emerald-50' : 'border-amber-300 bg-amber-50' }}">
    <p class="text-sm font-bold {{ $readyForApproval ? 'text-emerald-900' : 'text-amber-900' }}">
        {{ $readyForApproval ? 'Ready for platform approval' : 'Approval locked — requirements pending' }}
    </p>
    @if(! $readyForApproval)
        <ul id="delivery-approval-reasons-{{ $p->id }}" class="mt-2 list-disc space-y-1 pl-5 text-sm text-amber-900">
            @foreach($approvalBlockers as $reason)<li>{{ $reason }}</li>@endforeach
        </ul>
    @else
        <p class="mt-1 text-sm text-emerald-800">Required email and profile information is present. Approval remains subject to server-side identity checks.</p>
    @endif
    <div class="mt-3 flex flex-wrap items-end gap-3">
        <form method="POST" action="{{ route(explode('.',request()->route()->getName())[0].'.delivery-partner-approvals.approve',$p->id) }}">
            @csrf
            <button type="submit"
                @disabled(! $readyForApproval)
                @if(! $readyForApproval) aria-describedby="delivery-approval-reasons-{{ $p->id }}" title="Review the missing requirements above" @endif
                class="inline-flex min-h-11 items-center justify-center rounded-lg border px-5 py-3 text-sm font-bold shadow-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600 {{ $readyForApproval ? 'border-emerald-800 bg-emerald-700 text-white hover:bg-emerald-800' : 'cursor-not-allowed border-amber-500 bg-amber-100 text-amber-900' }}">
                {{ $readyForApproval ? 'Approve Profile' : 'Approval Locked' }}
            </button>
        </form>
        <form method="POST" action="{{ route(explode('.',request()->route()->getName())[0].'.delivery-partner-approvals.reject',$p->id) }}" class="flex flex-wrap items-end gap-2">
            @csrf
            <label class="text-sm font-medium">Reason
                <input name="reason" minlength="5" maxlength="500" required
                    class="mt-1 block min-h-11 rounded-lg border border-slate-400 bg-white px-3 py-2"
                    placeholder="Reason for rejection">
            </label>
            <button type="submit" class="min-h-11 rounded-lg border border-red-700 bg-white px-4 py-2 text-sm font-semibold text-red-800 hover:bg-red-50">Reject</button>
        </form>
    </div>
</div>
@endif
</article>
@empty<p class="rounded-lg bg-white p-4">No delivery profiles.</p>@endforelse
</div>{{ $partners->links() }}
</div>
@endsection
