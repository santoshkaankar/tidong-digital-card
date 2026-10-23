<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Delivery Profile - TIDONG</title>@vite(['resources/css/app.css','resources/js/app.js'])</head>
<body class="min-h-screen bg-slate-50 p-4 text-slate-900"><main class="mx-auto mt-8 max-w-xl rounded-2xl bg-white p-6 shadow">
<h1 class="text-2xl font-bold">Complete Delivery Partner Profile</h1><p class="mt-2 text-sm text-slate-600">Complete your vehicle and contact details. Platform approval and separate business approval are required before accepting any order.</p>
@if($errors->any())<p class="mt-4 rounded-lg bg-red-50 p-3 text-red-800">{{ $errors->first() }}</p>@endif
<form method="POST" action="{{ route('delivery.onboarding.update') }}" class="mt-5 space-y-4">@csrf
<label class="block text-sm font-semibold">Vehicle type<select name="vehicle_type" required class="mt-1 block w-full rounded-lg border p-3"><option value="">Select vehicle</option>@foreach(['bike'=>'Motorbike','scooter'=>'Scooter','bicycle'=>'Bicycle','car'=>'Car','van'=>'Van','on_foot'=>'On foot'] as $code=>$label)<option value="{{ $code }}" @selected(old('vehicle_type',$partner->vehicle_type)===$code)>{{ $label }}</option>@endforeach</select></label>
<label class="block text-sm font-semibold">Mobile number<input type="tel" name="phone" value="{{ old('phone',$partner->phone) }}" required maxlength="16" class="mt-1 block w-full rounded-lg border p-3"></label>
<button class="rounded-lg bg-blue-700 px-5 py-3 font-semibold text-white">Save profile</button></form>
<a class="mt-5 inline-block text-blue-700 underline" href="{{ route('registration.status') }}">Back to registration status</a></main></body></html>
