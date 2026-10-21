<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Review Business - Digital Platform</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 p-4 text-slate-800 sm:p-8">
<main class="mx-auto max-w-5xl space-y-6">
    <header class="flex flex-wrap items-center justify-between gap-4 rounded-xl bg-white p-6 shadow-sm">
        <div>
            <p class="text-sm text-slate-500">Business #{{ $businessRecord->id }}</p>
            <h1 class="text-2xl font-bold">{{ $businessRecord->name }}</h1>
            <p class="mt-1 text-sm text-slate-500">{{ $businessRecord->business_type }}</p>
        </div>
        <a href="{{ route(request()->segment(1) . '.businesses.index') }}" class="rounded-lg border px-4 py-2 text-sm">Back to Businesses</a>
    </header>
    @if (session('success'))
        <div class="rounded-lg bg-green-50 p-4 text-green-800">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="rounded-lg bg-red-50 p-4 text-red-800">
            @foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach
        </div>
    @endif
    <section class="grid gap-4 md:grid-cols-2">
        <div class="rounded-xl bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-lg font-semibold">Business details</h2>
            <dl class="space-y-2 text-sm">
                <div><dt class="font-semibold">Status</dt><dd>{{ ucfirst($businessRecord->status) }}</dd></div>
                <div><dt class="font-semibold">Orders</dt><dd>{{ $businessRecord->is_accepting_orders ? 'Enabled' : 'Disabled' }}</dd></div>
                <div><dt class="font-semibold">Slug</dt><dd class="break-all">{{ $businessRecord->slug }}</dd></div>
                <div><dt class="font-semibold">Created</dt><dd>{{ $businessRecord->created_at }}</dd></div>
            </dl>
        </div>
        <div class="rounded-xl bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-lg font-semibold">Vendor details</h2>
            <dl class="space-y-2 text-sm">
                <div><dt class="font-semibold">Vendor</dt><dd>{{ $businessRecord->vendor_name }}</dd></div>
                <div><dt class="font-semibold">Vendor code</dt><dd>{{ $businessRecord->vendor_code }}</dd></div>
                <div><dt class="font-semibold">Vendor status</dt><dd>{{ ucfirst($businessRecord->vendor_status) }}</dd></div>
                <div><dt class="font-semibold">Contact email</dt><dd>{{ $businessRecord->vendor_email ?: 'Not provided' }}</dd></div>
            </dl>
        </div>
    </section>
    <section class="rounded-xl bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold">Branches ({{ $branches->count() }})</h2>
        @forelse ($branches as $branch)
            <div class="mt-3 rounded-lg border p-4 text-sm">
                <strong>{{ $branch->name }}</strong> — {{ $branch->branch_code }}
                <p>{{ $branch->city ?: 'City not provided' }} · Status: {{ ucfirst($branch->status) }} · Orders: {{ $branch->is_accepting_orders ? 'Enabled' : 'Disabled' }}</p>
            </div>
        @empty
            <p class="mt-3 text-sm text-slate-500">No Branches added yet.</p>
        @endforelse
    </section>
    @if ($businessRecord->status === 'pending')
        <section class="rounded-xl bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold">Approval decision</h2>
            <p class="mt-1 text-sm text-slate-600">A Business must belong to an approved Vendor. Approval does not turn on orders.</p>
            <div class="mt-5 grid gap-6 md:grid-cols-2">
                @if ($canApprove)
                    <form method="POST" action="{{ route(request()->segment(1) . '.businesses.approve', ['business' => $businessRecord->id]) }}" class="space-y-3 rounded-lg border p-4">
                        @csrf
                        <h3 class="font-semibold">Approve</h3>
                        <label for="approve_reason" class="block text-sm">Review note (optional)</label>
                        <textarea id="approve_reason" name="reason" maxlength="500" rows="3" class="w-full rounded-lg border p-3"></textarea>
                        <button @disabled($businessRecord->vendor_status !== 'approved') class="rounded-lg bg-green-700 px-5 py-3 font-semibold text-white disabled:cursor-not-allowed disabled:opacity-50">Approve Business</button>
                        @if ($businessRecord->vendor_status !== 'approved')
                            <p class="text-sm text-amber-700">Approve Vendor first.</p>
                        @endif
                    </form>
                @endif
                @if ($canReject)
                    <form method="POST" action="{{ route(request()->segment(1) . '.businesses.reject', ['business' => $businessRecord->id]) }}" class="space-y-3 rounded-lg border p-4">
                        @csrf
                        <h3 class="font-semibold">Reject</h3>
                        <label for="reject_reason" class="block text-sm">Reason (required, at least 5 characters)</label>
                        <textarea id="reject_reason" name="reason" required minlength="5" maxlength="500" rows="3" class="w-full rounded-lg border p-3"></textarea>
                        <button class="rounded-lg bg-red-700 px-5 py-3 font-semibold text-white">Reject Business</button>
                    </form>
                @endif
            </div>
            @unless ($canApprove || $canReject)
                <p class="mt-4 text-slate-600">You can review this Business but have no decision permission.</p>
            @endunless
        </section>
    @endif
    <p class="text-sm text-slate-600">Transactions and orders are not created by this page.</p>
</main>
</body>
</html>
