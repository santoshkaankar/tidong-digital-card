@extends(request()->segment(1) === 'admin' ? 'layouts.admin' : 'layouts.platform-ops')
@section('title', 'Branch Change Approvals')
@section('page_heading', 'Branch Change Approvals')
@section('content')
<div class="mx-auto max-w-6xl space-y-5">
@php
    // Navigation uses the same secured route prefix as this review page.
    $dashboardRoute = str_starts_with($baseName, 'admin.') ? 'home'
        : (str_starts_with($baseName, 'staff-admin.') ? 'staff-admin.home' : 'employee.home');
@endphp
<header class="rounded-xl bg-white p-6 shadow-sm">
    <nav class="mb-4 flex flex-wrap gap-2" aria-label="Approval navigation">
        <a href="{{ route($dashboardRoute) }}" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-blue-700 hover:bg-slate-50">← Back to Dashboard</a>
        <a href="{{ route($baseName.'index') }}" class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">Branch Approvals</a>
    </nav>
    <h1 class="text-2xl font-bold">Branch &amp; Licence Change Approvals</h1>
    <p class="text-slate-600">Only approved requests change active records.</p>
</header>
<form method="GET" class="flex gap-3"><select name="status" class="rounded-lg border p-3"><option value="pending" @selected(request('status', 'pending')==='pending')>Pending</option><option value="approved" @selected(request('status')==='approved')>Approved</option><option value="rejected" @selected(request('status')==='rejected')>Rejected</option></select><button class="rounded-lg bg-blue-700 px-5 py-2 text-white">Filter</button></form>
<div class="overflow-x-auto rounded-xl bg-white shadow-sm"><table class="w-full text-left text-sm"><thead class="bg-slate-200"><tr><th class="p-3">Request</th><th class="p-3">Vendor</th><th class="p-3">Business / Branch</th><th class="p-3">Type</th><th class="p-3">Status</th><th class="p-3">Action</th></tr></thead><tbody>
@forelse($records as $item)<tr class="border-t"><td class="p-3">#{{ $item->id }}</td><td class="p-3">{{ $item->vendor->display_name }}</td><td class="p-3">{{ $item->branch->business->name }} / {{ $item->branch->name }}</td><td class="p-3">{{ str_replace('_',' ', $item->change_type) }}</td><td class="p-3">{{ ucfirst($item->status) }}</td><td class="p-3"><a class="text-blue-700 underline" href="{{ route($baseName.'show',$item->id) }}">Review</a></td></tr>
@empty <tr><td colspan="6" class="p-5 text-slate-600">No requests found.</td></tr> @endforelse</tbody></table></div>
{{ $records->links() }}
</div>
@endsection
