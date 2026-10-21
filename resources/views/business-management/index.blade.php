<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Business Approvals - Digital Platform</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 p-4 text-slate-800 sm:p-8">
<main class="mx-auto max-w-6xl space-y-6">
    <header class="flex flex-wrap items-center justify-between gap-4 rounded-xl bg-white p-6 shadow-sm">
        <div>
            <h1 class="text-2xl font-bold">Business Approvals</h1>
            <p class="mt-1 text-sm text-slate-500">Review Vendor Businesses before allowing setup to continue.</p>
        </div>
        <a href="{{ route(request()->segment(1) === 'admin' ? 'home' : request()->segment(1) . '.home') }}" class="rounded-lg border px-4 py-2 text-sm">Back to Dashboard</a>
    </header>
    @if (session('success'))
        <div class="rounded-lg bg-green-50 p-4 text-green-800">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="rounded-lg bg-red-50 p-4 text-red-800">
            @foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach
        </div>
    @endif
    <form method="GET" action="{{ route(request()->segment(1) . '.businesses.index') }}" class="flex flex-wrap items-end gap-3 rounded-xl bg-white p-5 shadow-sm">
        <div class="min-w-56 flex-1">
            <label for="search" class="mb-1 block text-sm font-medium">Search name, type, or Vendor</label>
            <input id="search" name="search" value="{{ request('search') }}" maxlength="100" class="w-full rounded-lg border p-3" placeholder="Search Businesses">
        </div>
        <div class="min-w-44">
            <label for="status" class="mb-1 block text-sm font-medium">Business status</label>
            <select id="status" name="status" class="w-full rounded-lg border p-3">
                <option value="">All statuses</option>
                @foreach (['pending', 'approved', 'rejected', 'suspended'] as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </div>
        <button class="rounded-lg bg-blue-700 px-5 py-3 font-semibold text-white">Search</button>
        <a class="rounded-lg border px-5 py-3" href="{{ route(request()->segment(1) . '.businesses.index') }}">Clear</a>
    </form>
    <section class="overflow-x-auto rounded-xl bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
            <thead class="bg-slate-50"><tr>
                <th class="p-4">Business</th><th class="p-4">Vendor</th><th class="p-4">Type</th>
                <th class="p-4">Status</th><th class="p-4">Orders</th><th class="p-4">Action</th>
            </tr></thead>
            <tbody class="divide-y divide-slate-100">
            @forelse ($businesses as $business)
                <tr>
                    <td class="p-4 font-medium">{{ $business->name }}</td>
                    <td class="p-4">{{ $business->vendor_name }}<span class="block text-xs text-slate-500">{{ $business->vendor_code }}</span></td>
                    <td class="p-4">{{ $business->business_type }}</td>
                    <td class="p-4">{{ ucfirst($business->status) }}</td>
                    <td class="p-4">{{ $business->is_accepting_orders ? 'Enabled' : 'Disabled' }}</td>
                    <td class="p-4"><a class="font-semibold text-blue-700 underline" href="{{ route(request()->segment(1) . '.businesses.show', ['business' => $business->id]) }}">Review</a></td>
                </tr>
            @empty
                <tr><td colspan="6" class="p-8 text-center text-slate-500">No Businesses found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </section>
    <div>{{ $businesses->links() }}</div>
    <p class="text-sm text-slate-600">Approving a Business never enables orders automatically. POS and Branch setup are separate steps.</p>
</main>
</body>
</html>
