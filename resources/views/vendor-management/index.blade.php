@extends(request()->segment(1) === 'admin' ? 'layouts.admin' : 'layouts.platform-ops')
@section('title', 'Vendor Management')
@section('page_heading', 'Vendor Management')
@section('content')
<div class="mx-auto max-w-7xl p-4 sm:p-8">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold">Vendor Management</h1>
            <p class="mt-1 text-sm text-slate-600">Search, review and manage Vendors.</p>
        </div>
        <a href="{{ route(request()->segment(1) === 'admin' ? 'home' : request()->segment(1) . '.home') }}"
           class="rounded-lg border bg-white px-4 py-2">Dashboard</a>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-100 p-3 text-green-800">{{ session('success') }}</div>
    @endif

    <form method="GET" action="{{ route(request()->segment(1) . '.vendors.index') }}"
          class="mb-5 flex flex-wrap gap-3 rounded-xl bg-white p-4 shadow-sm">
        <input name="search" value="{{ request('search') }}"
               placeholder="Vendor name, code, user or email"
               class="min-w-0 flex-1 rounded-lg border p-3">
        <select name="status" class="rounded-lg border p-3">
            <option value="">All statuses</option>
            @foreach(['pending', 'approved', 'rejected', 'suspended'] as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>
                    {{ ucfirst($status) }}
                </option>
            @endforeach
        </select>
        <button class="rounded-lg bg-blue-700 px-5 py-3 font-semibold text-white">Search</button>
        <a href="{{ route(request()->segment(1) . '.vendors.index') }}" class="rounded-lg border px-5 py-3">Clear</a>
    </form>

    <div class="overflow-x-auto rounded-xl bg-white shadow-sm">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-200">
            <tr>
                <th class="p-3">Code</th>
                <th class="p-3">Vendor</th>
                <th class="p-3">Account</th>
                <th class="p-3">Status</th>
                <th class="p-3">Action</th>
            </tr>
            </thead>
            <tbody>
            @forelse($vendors as $vendor)
                <tr class="border-t">
                    <td class="p-3">{{ $vendor->vendor_code }}</td>
                    <td class="p-3">{{ $vendor->display_name }}</td>
                    <td class="p-3">
                        <div>{{ $vendor->user_name ?? '—' }}</div>
                        <div class="text-slate-500">{{ $vendor->user_email ?? '' }}</div>
                    </td>
                    <td class="p-3">{{ ucfirst($vendor->status) }}</td>
                    <td class="p-3">
                        <a class="font-semibold text-blue-700 underline"
                           href="{{ route(request()->segment(1) . '.vendors.show', ['vendor' => $vendor->id]) }}">View details</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="p-8 text-center text-slate-500">
                        No Vendors found.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-5">{{ $vendors->links() }}</div>
</div>
@endsection
