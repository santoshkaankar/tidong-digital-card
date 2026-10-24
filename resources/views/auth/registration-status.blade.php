<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Account Status · TIDONG</title>@vite(['resources/css/app.css','resources/js/app.js'])</head>
<body class="min-h-screen bg-slate-50 p-4 text-slate-900"><main class="mx-auto mt-10 max-w-xl rounded-2xl bg-white p-6 shadow-md sm:p-10">
<h1 class="text-2xl font-bold">Registration Status</h1>
<p class="mt-2 text-sm text-slate-600">{{ $user->name }} · {{ ucwords(str_replace('_',' ', $type)) }}</p>
@if(session('success'))<p class="mt-4 rounded-xl bg-emerald-50 p-3 text-emerald-800">{{ session('success') }}</p>@endif
@if(! $user->email_verified_at)
<section class="mt-6 rounded-xl bg-amber-50 p-4"><h2 class="font-semibold">Verify your email</h2><p class="mt-2 text-sm">A verification link has been sent when mail is configured. Check your inbox.</p>
<form method="POST" action="{{ route('verification.send') }}" class="mt-3">@csrf<button class="rounded-lg bg-slate-900 px-4 py-2 text-white">Resend Verification Link</button></form></section>
@else<p class="mt-6 rounded-xl bg-emerald-50 p-4">Email verified.</p>@endif
@if($type==='customer' && $intent)
<section class="mt-4 rounded-xl border p-4"><h2 class="font-semibold">Affiliate Placement</h2><p class="mt-1 text-sm">Status: {{ str_replace('_',' ', $intent->status) }}</p>
@if($intent->status === 'needs_attention')<p class="mt-2 text-sm text-amber-700">Placement needs review: {{ $intent->last_error }}</p>@endif
</section>
@endif
@if($type==='delivery')
@php $delivery = \Illuminate\Support\Facades\DB::table('delivery_partners')->where('user_id',$user->id)->first(); @endphp
<section class="mt-4 rounded-xl border p-4"><h2 class="font-semibold">Delivery partner approval</h2>
<p class="mt-2 text-sm">Platform verification: {{ $delivery && $delivery->approved_at ? 'Approved' : 'Pending' }}. Business roster approval: required separately. Order acceptance: always manual.</p>
@if($delivery && $delivery->status==='pending' && ! $delivery->approved_at)
<a class="mt-3 inline-block rounded-lg bg-blue-700 px-4 py-2 text-white" href="{{ route('delivery.onboarding.edit') }}">Complete Delivery Profile</a>
@endif</section>
@endif
@if(in_array($type,['professional','service_provider'],true))
<section class="mt-4 rounded-xl border bg-white p-4">
<h2 class="font-semibold">{{ $type === 'professional' ? 'Professional' : 'Service Provider' }} Approval Steps</h2>
<ol class="mt-2 list-inside list-decimal space-y-2 text-sm">
<li>Email Verification: {{ $user->email_verified_at ? 'Complete' : 'Required' }}</li>
<li>Profile Submission: {{ $partnerApplication ? 'Submitted' : 'Pending' }}</li>
<li>Independent Admin Review: {{ $partnerApplication ? ucfirst($partnerApplication->status) : 'Not started' }}</li>
<li>Service Access: Subject to enabled modules and role-specific permissions</li>
</ol>
@if($partnerApplication && $partnerApplication->status === 'rejected')
<p class="mt-3 text-sm text-red-800">Review reason: {{ $partnerApplication->review_reason }}</p>
@endif
<a href="{{ route('partner.application.index') }}" class="mt-3 inline-flex rounded-lg bg-teal-700 px-4 py-3 font-semibold text-white">Open Profile Verification</a>
</section>
@endif
@if(in_array($type,['vendor','delivery','professional','service_provider'],true))<p class="mt-5 text-sm text-slate-600">Your account has been created. Final access to its module depends on the required profile and module authorization. Your account is not an outlet.</p>@endif
<div class="mt-6 flex flex-wrap gap-3"><a class="rounded-lg border px-4 py-2" href="{{ url('/') }}">Home</a>
<form method="POST" action="{{ route('logout') }}">@csrf<button class="rounded-lg border px-4 py-2">Logout</button></form></div>
</main></body></html>
