<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Branch Licences - {{ $branch->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 p-4 text-slate-800 sm:p-8">
<main class="mx-auto max-w-5xl space-y-5">
    <nav aria-label="Branch navigation" class="flex flex-wrap gap-2">
    <a href="{{ route('vendor.home') }}" class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white">← Back to Vendor Dashboard</a>
    <a href="{{ route('vendor.branch.edit', $branch->id) }}" class="rounded-lg border bg-white px-4 py-2 text-sm font-semibold text-blue-700">Request Branch Edit</a>
</nav>
    <header class="rounded-xl bg-white p-6 shadow-sm">
        <h1 class="text-2xl font-bold">Branch Registrations &amp; Licences</h1>
        <p class="mt-2 font-semibold">{{ $branch->business->name }} / {{ $branch->name }}</p>
        <p class="text-sm text-slate-600">Unique Branch Code: {{ $branch->branch_code }}</p>
        <p class="text-sm text-slate-600">{{ $branch->address_line }} · {{ $branch->district }} · {{ $branch->state }} · {{ $branch->pincode }}</p>
        <p class="mt-3 text-sm text-amber-800">New or changed licence details are **not applied until Admin approves**. Approval is platform review, not official verification with GST/FSSAI authorities. A GSTIN may be shared across eligible branches.</p>
    </header>
    @if (session('success'))<p class="rounded-lg bg-green-50 p-4 text-green-900">{{ session('success') }}</p>@endif
    @if ($errors->any())
      <div class="rounded-lg bg-red-50 p-4 text-red-800"><p class="font-semibold">Please check:</p>@foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>
    @endif

    @if($pending)
      <div class="rounded-lg bg-amber-50 p-4 text-amber-900">A {{ str_replace('_', ' ', $pending->change_type) }} request (#{{ $pending->id }}) is pending for this Branch. Wait for Admin approval before submitting further changes.</div>
    @endif
    <section class="rounded-xl bg-white p-6 shadow-sm">
        <h2 class="text-xl font-semibold">Request a new Licence</h2>
        <p class="mt-1 text-sm text-slate-600">Licence numbers are optional for branch creation. Enter the applicable registrations here when available.</p>
        <form method="POST" action="{{ route('vendor.branch.licenses.store', $branch->id) }}" class="mt-4 grid gap-4 md:grid-cols-2">
            @csrf
            <div><label class="block text-sm font-medium">Licence Type</label>
                <select name="license_type" class="mt-1 w-full rounded-lg border p-3" required>
                @foreach($types as $key => $caption)<option value="{{ $key }}" @selected(old('license_type') === $key)>{{ $caption }}</option>@endforeach
                </select>
            </div>
            <div><label class="block text-sm font-medium">Licence / Registration Number</label>
                <input name="license_number" value="{{ old('license_number') }}" required maxlength="100" class="mt-1 w-full rounded-lg border p-3" placeholder="Licence number"></div>
            <div><label class="block text-sm font-medium">Registered Legal Name (optional)</label>
                <input name="registered_name" value="{{ old('registered_name') }}" maxlength="255" class="mt-1 w-full rounded-lg border p-3"></div>
            <div><label class="block text-sm font-medium">Issuing Authority (optional)</label>
                <input name="issuing_authority" value="{{ old('issuing_authority') }}" maxlength="255" class="mt-1 w-full rounded-lg border p-3"></div>
            <div><label class="block text-sm font-medium">Issued On (optional)</label>
                <input type="date" name="issued_on" value="{{ old('issued_on') }}" class="mt-1 w-full rounded-lg border p-3"></div>
            <div><label class="block text-sm font-medium">Valid Until (optional)</label>
                <input type="date" name="expires_on" value="{{ old('expires_on') }}" class="mt-1 w-full rounded-lg border p-3"></div>
            <div class="md:col-span-2"><label class="block text-sm font-medium">Notes (optional)</label>
                <textarea name="notes" maxlength="2000" rows="2" class="mt-1 w-full rounded-lg border p-3">{{ old('notes') }}</textarea></div>
            <button class="rounded-lg bg-blue-700 px-5 py-3 font-semibold text-white md:col-span-2">Submit New Licence for Approval</button>
        </form>
    </section>

    <section class="space-y-3">
      <h2 class="text-xl font-semibold">Saved Licences ({{ $branch->licenses->count() }})</h2>
      @forelse ($branch->licenses as $license)
        <article class="rounded-xl bg-white p-5 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <h3 class="font-semibold">{{ $types[$license->license_type] ?? ucfirst($license->license_type) }}</h3>
                <span class="text-sm text-slate-600">Added {{ $license->created_at?->format('d M Y') }}</span>
            </div>
            <p class="mt-2 font-mono">{{ $license->license_number }}</p>
            @if($license->expires_on && $license->expires_on->isPast())
              <p class="text-sm text-red-700">Recorded expiry date has passed</p>
            @endif
            <form method="POST" action="{{ route('vendor.branch.licenses.update', [$branch->id, $license->id]) }}" class="mt-4 grid gap-3 border-t pt-4 md:grid-cols-2">
              @csrf @method('PUT')
              <div><label class="text-sm font-medium">Registered Legal Name</label><input name="registered_name" value="{{ $license->registered_name }}" maxlength="255" class="mt-1 w-full rounded-lg border p-3"></div>
              <div><label class="text-sm font-medium">Issuing Authority</label><input name="issuing_authority" value="{{ $license->issuing_authority }}" maxlength="255" class="mt-1 w-full rounded-lg border p-3"></div>
              <div><label class="text-sm font-medium">Issued On</label><input type="date" name="issued_on" value="{{ $license->issued_on?->format('Y-m-d') }}" class="mt-1 w-full rounded-lg border p-3"></div>
              <div><label class="text-sm font-medium">Valid Until</label><input type="date" name="expires_on" value="{{ $license->expires_on?->format('Y-m-d') }}" class="mt-1 w-full rounded-lg border p-3"></div>
              <div class="md:col-span-2"><label class="text-sm font-medium">Notes</label><textarea name="notes" maxlength="2000" rows="2" class="mt-1 w-full rounded-lg border p-3">{{ $license->notes }}</textarea></div>
              <div class="md:col-span-2"><button class="rounded-lg border border-blue-600 px-4 py-2 text-blue-800">Request Licence Update (Admin Approval)</button></div>
            </form>
        </article>
      @empty
        <p class="rounded-xl bg-white p-5 text-slate-600">No licences recorded for this branch.</p>
      @endforelse
    </section>
    <section class="rounded-xl bg-white p-5 shadow-sm">
      <h2 class="text-xl font-semibold">Licence Request History</h2>
      @forelse($history as $item)
        <p class="mt-2 text-sm">Request #{{ $item->id }} ({{ str_replace('_', ' ', $item->change_type) }}) — <strong>{{ ucfirst($item->status) }}</strong> @if($item->review_reason) — {{ $item->review_reason }} @endif</p>
      @empty <p class="mt-2 text-slate-500">No previous licence requests.</p> @endforelse
    </section>
</main>
</body>
</html>
