@extends(request()->segment(1) === 'admin' ? 'layouts.admin' : 'layouts.platform-ops')
@section('title', 'Vendor Details')
@section('page_heading', 'Vendor Details')
@section('content')
<div class="mx-auto max-w-4xl p-4 sm:p-8">
    <a href="{{ route(request()->segment(1) . '.vendors.index') }}" class="text-blue-700 underline">← Back to Vendors</a>

    <div class="mt-5 rounded-xl bg-white p-5 shadow-sm sm:p-8">
        <h1 class="text-2xl font-bold">Vendor Details</h1>

        @if(session('success'))
            <div class="mt-4 rounded-lg bg-green-100 p-3 text-green-800">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="mt-4 rounded-lg bg-red-100 p-3 text-red-800">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <dl class="mt-5 grid gap-4 sm:grid-cols-2">
            <div><dt class="text-sm text-slate-500">Vendor ID</dt><dd class="font-semibold">{{ $vendorRecord->id }}</dd></div>
            <div><dt class="text-sm text-slate-500">Vendor Code</dt><dd class="font-semibold">{{ $vendorRecord->vendor_code }}</dd></div>
            <div><dt class="text-sm text-slate-500">Display Name</dt><dd class="font-semibold">{{ $vendorRecord->display_name }}</dd></div>
            <div><dt class="text-sm text-slate-500">Status</dt><dd class="font-semibold">{{ ucfirst($vendorRecord->status) }}</dd></div>
            <div><dt class="text-sm text-slate-500">Account Holder</dt><dd>{{ $vendorRecord->user_name ?? '—' }}</dd></div>
            <div><dt class="text-sm text-slate-500">Email</dt><dd>{{ $vendorRecord->user_email ?? '—' }}</dd></div>
            <div><dt class="text-sm text-slate-500">1. Email Verification</dt><dd class="font-semibold {{ $vendorRecord->user_email_verified_at ? 'text-emerald-700' : 'text-amber-800' }}">{{ $vendorRecord->user_email_verified_at ? 'Verified' : 'Pending — email link required' }}</dd></div>
            <div><dt class="text-sm text-slate-500">Approved At</dt><dd>{{ $vendorRecord->approved_at ?? '—' }}</dd></div>
            <div><dt class="text-sm text-slate-500">Approved By (User ID)</dt><dd>{{ $vendorRecord->approved_by ?? '—' }}</dd></div>
        </dl>

        @if($vendorRecord->status === 'pending' && ($canApprove || $canReject))
            <div class="mt-8 grid gap-5 border-t pt-6 md:grid-cols-2">
                @if($canApprove)
                <form method="POST" action="{{ route(request()->segment(1) . '.vendors.approve', ['vendor' => $vendorRecord->id]) }}"
                      onsubmit="return confirm('Approve this Vendor?')">
                    @csrf
                    <h2 class="mb-2 font-bold">Approve Vendor</h2>
                    <label class="mb-2 block text-sm" for="approve-reason">Optional note</label>
                    <textarea id="approve-reason" name="reason" maxlength="500"
                              class="w-full rounded-lg border p-3"></textarea>
                    @if(! $vendorRecord->user_email_verified_at)
                    <p class="mt-2 rounded-lg border border-amber-300 bg-amber-50 p-3 text-sm text-amber-900">Approval locked: this vendor must verify their registered email first. Do not bypass the email ownership check.</p>
                    @endif
                    <button type="submit" @disabled(! $vendorRecord->user_email_verified_at)
                        class="mt-3 min-h-11 rounded-lg px-5 py-3 font-semibold {{ $vendorRecord->user_email_verified_at ? 'bg-green-700 text-white hover:bg-green-800' : 'cursor-not-allowed border border-amber-500 bg-amber-100 text-amber-900' }}">
                        {{ $vendorRecord->user_email_verified_at ? 'Approve Vendor' : 'Email Verification Required' }}
                    </button>
                </form>

                @endif
                @if($canReject)
                <form method="POST" action="{{ route(request()->segment(1) . '.vendors.reject', ['vendor' => $vendorRecord->id]) }}"
                      onsubmit="return confirm('Reject this Vendor?')">
                    @csrf
                    <h2 class="mb-2 font-bold">Reject Vendor</h2>
                    <label class="mb-2 block text-sm" for="reject-reason">Reason (required)</label>
                    <textarea id="reject-reason" name="reason" required minlength="5" maxlength="500"
                              class="w-full rounded-lg border p-3"></textarea>
                    <button class="mt-3 rounded-lg bg-red-700 px-5 py-3 font-semibold text-white">
                        Reject
                    </button>
                </form>
                @endif
            </div>
        @endif
    </div>

    <div class="mt-6 rounded-xl bg-white p-5 shadow-sm">
        <h2 class="text-xl font-bold">Businesses</h2>
        @forelse($businesses as $business)
            <div class="mt-3 rounded-lg border p-4">
                <div class="font-semibold">{{ $business->name }}</div>
                <div class="text-sm text-slate-600">
                    Code/Slug: {{ $business->slug ?? '—' }} · Status: {{ $business->status }}
                </div>
            </div>
        @empty
            <p class="mt-3 text-slate-500">No Businesses found for this Vendor.</p>
        @endforelse
    </div>
</div>
@endsection
