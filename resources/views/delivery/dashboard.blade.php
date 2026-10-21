<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Delivery Partner Dashboard — TIDONG</title>@vite(['resources/css/app.css','resources/js/app.js'])</head>
<body class="min-h-screen bg-slate-100 text-slate-800"><header class="bg-slate-900 p-4 text-white"><div class="mx-auto flex max-w-5xl justify-between gap-3"><h1 class="text-xl font-semibold">Delivery Partner Dashboard</h1><form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="rounded border border-slate-400 px-3 py-1">Logout</button></form></div></header>
<main class="mx-auto max-w-5xl space-y-5 p-4">
    <p class="text-sm text-slate-600">Only deliveries assigned to your approved partner account appear here.</p>
    <form method="POST" action="{{ route('delivery.availability') }}" class="rounded bg-white p-3 flex flex-wrap items-center gap-3">@csrf<span>Availability: <strong>{{ $partner->is_available ? 'Online' : 'Offline' }}</strong></span><input type="hidden" name="available" value="{{ $partner->is_available ? 0 : 1 }}"><button class="rounded border px-3 py-1" type="submit">Go {{ $partner->is_available ? 'Offline' : 'Online' }}</button></form>
    @if(session('success'))<p class="rounded bg-green-50 p-3 text-green-800">{{ session('success') }}</p>@endif
    @if($errors->any())<p class="rounded bg-red-50 p-3 text-red-800">{{ $errors->first() }}</p>@endif
    @forelse($jobs as $job)<article class="rounded-xl bg-white p-5 shadow-sm space-y-2">
        <div class="flex flex-wrap items-center justify-between gap-2"><h2 class="font-semibold">{{ $job->reference }}</h2><span class="rounded bg-slate-100 px-2 py-1 text-sm">{{ ucfirst(str_replace('_',' ',$job->status)) }}</span></div>
        <p class="text-sm">Pickup Branch: {{ $job->branch_name }}</p>
        @if(in_array($job->status, ['assigned', 'accepted', 'picked_up'], true))
        <p class="text-sm">Recipient: {{ $job->recipient_name }} — {{ $job->recipient_phone }}</p>
        <p class="text-sm whitespace-pre-wrap">Drop-off: {{ $job->dropoff_address }}</p>
        @if($job->delivery_note)<p class="text-sm">Note: {{ $job->delivery_note }}</p>@endif
        @else
        <p class="text-sm text-slate-500">Recipient contact details are hidden for completed or inactive deliveries.</p>
        @endif
        <div class="flex flex-wrap gap-2 pt-2">
            @php($actions = ['assigned'=>['accepted'=>'Accept','declined'=>'Decline'],'accepted'=>['picked_up'=>'Mark Picked Up'],'picked_up'=>['delivered'=>'Mark Delivered','failed'=>'Report Failed']][$job->status] ?? [])
            @foreach($actions as $status=>$label)<form method="POST" action="{{ route('delivery.jobs.status',$job->id) }}" class="flex flex-wrap gap-2">@csrf<input type="hidden" name="status" value="{{ $status }}">
            @if($status === 'delivered')<input class="rounded border p-2" name="handoff_pin" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" placeholder="Recipient 6-digit PIN" required><input class="rounded border p-2" name="recipient_name" maxlength="160" placeholder="Received by (name)" required>@endif
            @if($status === 'failed')<input class="rounded border p-2" name="failure_reason" maxlength="500" placeholder="Failure reason" required>@endif
            <button class="rounded bg-blue-700 px-3 py-2 text-sm text-white" type="submit">{{ $label }}</button></form>@endforeach
        </div>
    </article>
    @empty<div class="rounded-xl bg-white p-8 text-center text-slate-500">No assigned deliveries.</div>@endforelse
    {{ $jobs->links() }}
</main></body></html>
