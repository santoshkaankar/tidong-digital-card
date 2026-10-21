<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $business->name }} · Tidong Business Workspace</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-800">
<div class="mx-auto flex max-w-screen-2xl flex-col items-start gap-5 p-4 xl:flex-row xl:p-6">
    @include('vendor.partials.sidebar')
    <main class="w-full min-w-0 flex-1 space-y-5">
        <header class="rounded-2xl bg-white p-6 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-widest text-blue-700">Dedicated Business Dashboard</p>
            <h1 class="mt-1 text-3xl font-bold">{{ $business->name }}</h1>
            <div class="mt-2 flex flex-wrap gap-3 text-sm text-slate-600">
                <span>Business type: {{ ucwords(str_replace('_', ' ', $business->business_type)) }}</span>
                <span>Status: <strong>{{ ucfirst($business->status) }}</strong></span>
                <span>Branches: {{ $branches->count() }}</span>
            </div>
            @if ($business->status !== 'approved')
                <p class="mt-4 rounded-lg bg-amber-50 p-3 text-sm text-amber-900">This Business is awaiting approval. No new public orders are enabled.</p>
            @endif
        </header>

        <section class="rounded-2xl bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold">Select Branch</h2>
            <p class="mb-4 mt-1 text-sm text-slate-500">Choose a Branch to see only its business-specific operations.</p>
            <div class="flex flex-wrap gap-2">
                @forelse ($branches as $option)
                    <a href="{{ route('vendor.business.workspace', ['business' => $business->id, 'branch' => $option->id]) }}"
                       class="rounded-lg border px-4 py-2 text-sm {{ $navBranch && (int) $navBranch->id === (int) $option->id ? 'border-blue-700 bg-blue-700 font-semibold text-white' : 'bg-white text-slate-700' }}">{{ $option->name }}</a>
                @empty
                    <p class="rounded-lg bg-slate-50 p-4 text-sm text-slate-600">No Branch created yet. Add one from <a class="font-semibold text-blue-700 underline" href="{{ route('vendor.home') }}#vendor-businesses">My Businesses</a>.</p>
                @endforelse
            </div>
        </section>

        @php
            $typeGroups = config('tidong_business_navigation.type_groups', []);
            $groups = config('tidong_business_navigation.groups', []);
            $kind = $typeGroups[$business->business_type] ?? 'services';
            $group = $groups[$kind] ?? ['title' => 'Business', 'items' => []];
        @endphp
        <section class="rounded-2xl bg-white p-6 shadow-sm">
            <h2 class="text-xl font-bold">{{ $group['title'] }} Menu</h2>
            <p class="mt-1 text-sm text-slate-500">Only modules relevant to this Business are listed in its dedicated Sidebar.</p>
            @if ($kind === 'sublimation')
                <a class="mt-5 inline-block rounded-lg bg-rose-600 px-5 py-3 text-sm font-bold text-white"
                   href="{{ route('vendor.sublimation.index', ['business' => $business->id] + ($navBranch ? ['branch' => $navBranch->id] : [])) }}">
                    Open Sublimation Studio
                </a>
                <p class="mt-3 text-sm text-slate-500">Manage products, customer design approvals, internal job cards and production status.</p>
            @elseif ($kind === 'restaurant')
                <p class="mt-4 text-sm text-slate-600">POS, Menu Library, Kitchen KDS and Table QR links are available in the Sidebar when the Business is approved and a Branch is selected.</p>
            @else
                <p class="mt-4 rounded-lg border bg-slate-50 p-4 text-sm text-slate-600">The dedicated menu structure is ready. The listed Business-specific operations will be activated only when their backend modules and permissions are implemented.</p>
            @endif
        </section>
        <section class="rounded-2xl bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold">Branch Administration</h2>
            <p class="mt-2 text-sm text-slate-600">Opening balances and stock reconciliation belong to the outlet owner.</p>
            <div class="mt-3 space-y-3">
                @forelse ($branches as $option)
                    <div class="flex flex-wrap items-center justify-between gap-3 rounded-lg border p-3">
                        <div><p class="font-semibold">{{ $option->name }}</p><p class="text-xs text-slate-500">{{ $option->city ?: 'City not specified' }} · {{ $option->branch_code }}</p></div>
                        <div class="flex flex-wrap gap-2 text-sm">
                            <a class="rounded-lg border px-3 py-2 text-blue-700" href="{{ route('vendor.opening.index', [$business->id, $option->id]) }}">Outlet Opening &amp; Stock</a>
                            <a class="rounded-lg border px-3 py-2 text-blue-700" href="{{ route('vendor.branch.edit', $option->id) }}">Request Branch Edit</a>
                            <a class="rounded-lg border px-3 py-2 text-blue-700" href="{{ route('vendor.branch.licenses.index', $option->id) }}">Licences</a>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">No Branch yet.</p>
                @endforelse
            </div>
        </section>
    </main>
</div>
</body>
</html>
