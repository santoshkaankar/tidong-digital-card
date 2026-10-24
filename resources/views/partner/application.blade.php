<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Partner Profile Verification | TIDONG</title>@vite(['resources/css/app.css','resources/js/app.js'])</head>
<body class="bg-slate-50 p-4 text-slate-900"><main class="mx-auto max-w-2xl space-y-5 rounded-2xl bg-white p-5 shadow-sm sm:p-8">
<a class="font-semibold text-teal-800 underline" href="{{ route('registration.status') }}">← Account Status</a>
<h1 class="text-2xl font-bold">{{ $type==='professional'?'Professional':'Service Provider' }} Verification</h1>
<div class="grid gap-2 text-sm sm:grid-cols-3">
<div class="rounded-xl border p-3 {{ auth()->user()->email_verified_at?'bg-emerald-50':'bg-amber-50' }}">1. Email: {{ auth()->user()->email_verified_at?'Verified':'Verification needed' }}</div>
<div class="rounded-xl border p-3">2. Profile: {{ $record?'Submitted':'Required' }}</div>
<div class="rounded-xl border p-3">3. Review: {{ $record?ucfirst($record->status):'Not started' }}</div>
</div>
@if($errors->any())<ul role="alert" class="rounded-lg bg-red-50 p-4 text-red-900">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>@endif
@if(!auth()->user()->email_verified_at)
<div class="rounded-lg border border-amber-300 bg-amber-50 p-4"><strong>Email Verification is step 1.</strong><p>Open the verification link sent to your email before submitting your profile.</p></div>
@elseif(!$enabled)<p class="rounded-lg border border-amber-300 bg-amber-50 p-4">Applications are temporarily closed by the Administrator.</p>
@elseif($record && $record->status!=='rejected')
<div class="rounded-lg bg-blue-50 p-4">Current application: <strong>{{ ucfirst($record->status) }}</strong>. An independent reviewer will decide after verification. No automatic access is granted.</div>
@else
@if($record && $record->status==='rejected')<p class="rounded-lg bg-amber-50 p-3">Previous review: Rejected. {{ $record->review_reason }}</p>@endif
<form method="POST" action="{{ route('partner.application.store') }}" class="space-y-4">@csrf
<label class="block font-semibold">Service or professional title<input required name="service_title" maxlength="160" value="{{ old('service_title',$record->service_title??'') }}" class="mt-1 block w-full rounded-lg border p-3"></label>
<label class="block font-semibold">Category / specialization<input required name="category" maxlength="120" value="{{ old('category',$record->category??'') }}" class="mt-1 block w-full rounded-lg border p-3"></label>
<label class="block font-semibold">Professional registration / service reference<input required name="registration_reference" maxlength="160" value="{{ old('registration_reference',$record->registration_reference??'') }}" class="mt-1 block w-full rounded-lg border p-3"></label>
<label class="block font-semibold">Experience and service description<textarea required name="experience_description" minlength="20" maxlength="3000" rows="5" class="mt-1 block w-full rounded-lg border p-3">{{ old('experience_description',$record->experience_description??'') }}</textarea></label>
<p class="text-sm text-slate-600">Provide truthful information. Approval requires an independently checked record. Do not submit passwords or OTPs.</p>
<button type="submit" class="min-h-12 rounded-lg bg-teal-700 px-5 py-3 font-bold text-white hover:bg-teal-800">Submit for Verification</button></form>
@endif
</main></body></html>