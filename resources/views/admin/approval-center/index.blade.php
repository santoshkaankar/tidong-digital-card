@extends('layouts.admin')
@section('title','Approval & Verification Center')
@section('page_heading','Approval & Verification Center')
@section('content')
<div class="mx-auto max-w-6xl space-y-5">
<header class="rounded-2xl border bg-white p-5 shadow-sm"><h1 class="text-2xl font-bold">Approval &amp; Verification Center</h1>
<p class="mt-2 text-sm text-slate-600">One queue for identity and platform reviews, in a consistent order. Every existing approval action retains its own permissions, independent checks and audit history. Vendor dispatch and company internals remain inaccessible.</p></header>
<section class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
@foreach([
 ['title'=>'1 · Email Verification','key'=>'email','label'=>'Unverified accounts','href'=>null],
 ['title'=>'2 · Vendor Identity','key'=>'vendor','label'=>'Vendor applications','href'=>route('admin.vendors.index')],
 ['title'=>'3 · Business Approval','key'=>'business','label'=>'Business applications','href'=>route('admin.businesses.index')],
 ['title'=>'4 · Branch / Licence','key'=>'branch','label'=>'Change requests','href'=>route('admin.branch-changes.index')],
 ['title'=>'5 · Delivery Partner','key'=>'delivery','label'=>'Platform profile checks','href'=>route('admin.delivery-partner-approvals.index')],
 ['title'=>'6 · Professional','key'=>'professional','label'=>'Professional profile review','href'=>route('admin.approval-center.queue','professional')],
 ['title'=>'7 · Service Provider','key'=>'service_provider','label'=>'Service provider review','href'=>route('admin.approval-center.queue','service_provider')],
 ['title'=>'8 · Professional Qualifications','key'=>'qualification','label'=>'Legal qualification review','href'=>route('admin.legal.qualifications.index')],
] as $group)
<article class="rounded-xl border bg-white p-4 shadow-sm">
<h2 class="font-bold">{{ $group['title'] }}</h2><p class="mt-1 text-sm text-slate-500">{{ $group['label'] }}</p>
<p class="my-3 text-2xl font-bold">{{ $counts[$group['key']] ?? 0 }}</p>
@if($group['href'])<a href="{{ $group['href'] }}" class="inline-flex min-h-11 items-center rounded-lg border border-teal-700 px-4 py-2 font-semibold text-teal-800 hover:bg-teal-50">Review Group →</a>
@else <span class="text-sm text-slate-500">Users must use their signed email verification link. Admin cannot bypass this step here.</span>@endif
</article>@endforeach
</section>
<section class="rounded-2xl border bg-white p-5"><h2 class="text-lg font-bold">Step 1 · Email Verification Pending</h2>
<p class="my-2 text-sm text-slate-600">Shown for diagnosis only. Verification must be completed from each user's inbox.</p>
<div class="overflow-x-auto"><table class="w-full min-w-[450px] text-left text-sm"><thead><tr class="border-b"><th class="p-2">Account</th><th class="p-2">Type</th><th class="p-2">Required next action</th></tr></thead><tbody>
@forelse($blocked as $u)<tr class="border-b"><td class="p-2">{{ $u->name }} ({{ $u->email }})</td><td class="p-2">{{ ucwords(str_replace('_',' ',$u->code)) }}</td><td class="p-2 text-amber-900">Verify email from the user's own inbox</td></tr>
@empty<tr><td colspan="3" class="p-3 text-slate-500">No unverified partner accounts.</td></tr>@endforelse</tbody></table></div>
</section>
@if(!$enabled)<p class="rounded-xl border border-amber-300 bg-amber-50 p-4 text-sm text-amber-900">Professional / Service Provider review actions are currently OFF in Module Management. All other review routes retain their individual controls.</p>@endif
</div>
@endsection