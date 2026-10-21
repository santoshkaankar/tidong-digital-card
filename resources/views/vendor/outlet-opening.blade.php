<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Outlet Opening & Stock · TIDONG</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 text-slate-800">
<main class="mx-auto max-w-6xl space-y-5 p-5">
  <header class="rounded-xl bg-white p-5 shadow-sm">
    <a class="text-blue-700 underline" href="{{ route('vendor.business.workspace', ['business' => $business, 'branch' => $branch]) }}">← Business Workspace</a>
    <h1 class="mt-3 text-2xl font-bold">Outlet Opening Balances</h1>
    <p>{{ $outlet->business_name }} · {{ $outlet->name }}</p>
    <p class="mt-2 text-sm">Record the opening as of the first business day. New operations continue while previous balances are reconciled. Changes are tracked below.</p>
  </header>
  @if(session('success')) <p class="rounded-lg bg-green-50 p-3 text-green-800">{{ session('success') }}</p> @endif
  @if($errors->any())
    <div class="rounded-lg bg-red-50 p-4 text-red-800"><strong>Could not save:</strong>
      <ul class="list-disc pl-5">@foreach($errors->all() as $message)<li>{{ $message }}</li>@endforeach</ul>
    </div>
  @endif
  <section class="rounded-xl bg-white p-5 shadow-sm">
    <h2 class="text-lg font-bold">Opening Period</h2>
    @if(!$state)
      <form method="post" action="{{ route('vendor.opening.begin', [$business, $branch]) }}" class="mt-3 flex flex-wrap items-end gap-3">
        @csrf
        <label class="flex flex-col gap-1">Start date <input type="date" name="opening_date" value="{{ old('opening_date', date('Y-m-d')) }}" required class="rounded border p-2"></label>
        <button class="rounded bg-blue-700 px-4 py-2 text-white">Start Opening Period</button>
      </form>
    @else
      <p class="mt-2">Opening date: <strong>{{ $state->opening_date }}</strong> · State:
        <strong>{{ $state->is_locked ? 'LOCKED' : 'OPEN — editable' }}</strong> · Lock revision {{ $state->lock_version }}</p>
      <form method="post" action="{{ route('vendor.opening.lock', [$business, $branch]) }}" class="mt-3 flex flex-wrap items-end gap-3">
        @csrf <input type="hidden" name="lock" value="{{ $state->is_locked ? '0' : '1' }}">
        <label class="flex flex-col gap-1">Reason for {{ $state->is_locked ? 'unlock' : 'final lock' }}
          <input class="w-80 rounded border p-2" type="text" name="reason" required minlength="10" maxlength="500" placeholder="Explain why this action is needed"></label>
        <button class="rounded px-4 py-2 text-white {{ $state->is_locked ? 'bg-orange-700' : 'bg-slate-900' }}">{{ $state->is_locked ? 'Unlock for Correction' : 'Final Lock Opening' }}</button>
      </form>
      <p class="mt-2 text-xs text-slate-600">Owner can unlock with a reason. Locked opening cannot be edited, but normal operational entries are not blocked. Closed/filed periods must use compliant adjustment entries.</p>
    @endif
  </section>
  @if($state)
    <section class="rounded-xl bg-white p-5 shadow-sm">
      <h2 class="text-lg font-bold">Ledger Opening Balances</h2>
      <p class="text-sm">Amounts use INR. Debit and Credit openings must balance before Final Lock.</p>
      <p class="mt-1 text-xs text-amber-700">Current ledger totals reflect opening changes, but previously posted transaction snapshots remain immutable. Finance running-balance reports and period-closing controls require further integration before production use.</p>
      <p class="mt-1 text-xs text-slate-600">New ledgers may be created after locking, but their opening is fixed at zero until an authorized, compliant correction.</p>
        <form method="post" action="{{ route('vendor.opening.ledgers.create', [$business, $branch]) }}" class="mt-3 flex flex-wrap items-end gap-2">
          @csrf
          <label class="flex flex-col">Ledger name <input class="rounded border p-2" name="ledger_name" required maxlength="180" minlength="2"></label>
          <label class="flex flex-col">Ledger group <select name="ledger_group_id" class="rounded border p-2" required>
            <option value="">Select group</option>@foreach($groups as $g)<option value="{{ $g->id }}">{{ $g->group_name }} ({{ $g->nature }})</option>@endforeach
          </select></label>
          <button class="rounded bg-blue-700 px-3 py-2 text-white">Add Ledger</button>
        </form>
        @if($groups->isEmpty())<p class="mt-2 text-amber-700">Ledger groups must be configured before adding finance ledgers.</p>@endif
        @if($existingLedgers->isNotEmpty())
          <form method="post" action="{{ route('vendor.opening.ledgers.attach', [$business, $branch]) }}" class="mt-3 flex flex-wrap items-end gap-2">
            @csrf <label class="flex flex-col">Existing outlet ledger
              <select name="ledger_id" required class="rounded border p-2">
                <option value="">Select an existing ledger</option>
                @foreach($existingLedgers as $existing)<option value="{{ $existing->id }}">{{ $existing->ledger_name }} — existing opening ₹{{ $existing->opening_balance }}</option>@endforeach
              </select>
            </label><button class="rounded bg-blue-700 px-3 py-2 text-white">Register Existing Ledger</button>
          </form>
        @endif
      <div class="mt-4 space-y-3">
        @forelse($ledgers as $l)
          <div class="rounded-lg border p-3">
            <strong>{{ $l->ledger_name }}</strong> · <span class="text-sm">{{ $l->group_name }}</span>
            <p class="text-sm">Opening: {{ strtoupper($l->direction) }} ₹{{ \App\Services\Opening\OpeningNumber::formatPaise((int) $l->amount_paise) }} · Current finance balance: ₹{{ $l->current_balance }}</p>
            @if(!$state->is_locked)
              <form method="post" action="{{ route('vendor.opening.ledgers.opening', [$business, $branch, $l->id]) }}" class="mt-2 flex flex-wrap items-end gap-2">
                @csrf
                <label>Direction <select class="rounded border p-2" name="direction"><option value="debit" @selected($l->direction==='debit')>Debit</option><option value="credit" @selected($l->direction==='credit')>Credit</option></select></label>
                <label>Amount <input class="w-32 rounded border p-2" name="amount" value="{{ \App\Services\Opening\OpeningNumber::formatPaise((int) $l->amount_paise) }}" required></label>
                <label>Reason <input class="rounded border p-2" name="reason" required minlength="5" placeholder="Old records reconciled"></label>
                <button class="rounded bg-blue-700 px-3 py-2 text-white">Save Opening</button>
              </form>
            @endif
          </div>
        @empty <p class="text-slate-500">No opening ledgers yet.</p> @endforelse
      </div>
    </section>
    <section class="rounded-xl bg-white p-5 shadow-sm">
      <h2 class="text-lg font-bold">Stock Opening</h2>
      <p class="text-sm">Create a raw-material or packaging stock item, then record its opening quantity and unit cost. Menu items are not automatically raw-material stock.</p>
      <p class="mt-1 text-xs text-slate-600">New stock items may be added after locking, with zero opening. Normal purchase/consumption posting is a separate integration.</p>
        <form method="post" action="{{ route('vendor.opening.stock.create', [$business, $branch]) }}" class="mt-3 flex flex-wrap items-end gap-2">
          @csrf
          <label>SKU <input class="w-28 rounded border p-2" name="sku" required maxlength="80"></label>
          <label>Name <input class="rounded border p-2" name="name" required maxlength="180"></label>
          <label>Unit <select class="rounded border p-2" name="unit">@foreach(['pcs', 'kg', 'g', 'l', 'ml', 'm'] as $unit)<option value="{{ $unit }}">{{ $unit }}</option>@endforeach</select></label>
          <button class="rounded bg-blue-700 px-3 py-2 text-white">Add Stock Item</button>
        </form>
      <div class="mt-4 space-y-3">
        @forelse($stock as $s)
          <div class="rounded-lg border p-3">
            <strong>{{ $s->name }}</strong> · {{ $s->sku }} ({{ $s->unit }})
            <p class="text-sm">Opening: {{ $s->opening_quantity }} · Available: {{ $s->quantity_on_hand }} · Opening value: ₹{{ \App\Services\Opening\OpeningNumber::formatPaise((int) $s->opening_stock_value_paise) }}</p>
            @if(!$state->is_locked)
              <form method="post" action="{{ route('vendor.opening.stock.opening', [$business, $branch, $s->id]) }}" class="mt-2 flex flex-wrap items-end gap-2">
                @csrf
                <label>Opening Qty <input class="w-32 rounded border p-2" name="quantity" value="{{ $s->opening_quantity }}" required></label>
                <label>Cost / Unit (₹) <input class="w-32 rounded border p-2" name="unit_cost" value="{{ \App\Services\Opening\OpeningNumber::formatPaise((int) $s->opening_unit_cost_paise) }}" required></label>
                <label>Reason <input class="rounded border p-2" name="reason" required minlength="5" placeholder="Physical stock matched"></label>
                <button class="rounded bg-blue-700 px-3 py-2 text-white">Save Opening</button>
              </form>
            @endif
          </div>
        @empty <p class="text-slate-500">No stock register items yet.</p> @endforelse
      </div>
    </section>
  @endif
  <section class="rounded-xl bg-white p-5 shadow-sm">
    <h2 class="text-lg font-bold">Audit History — All Changes</h2>
    <form method="get" class="my-3 flex flex-wrap items-end gap-2 text-sm">
      <label>Employee/User ID <input type="number" min="1" name="audit_actor" value="{{ request('audit_actor') }}" class="w-32 rounded border p-2"></label>
      <label>From <input type="date" name="audit_from" value="{{ request('audit_from') }}" class="rounded border p-2"></label>
      <label>To <input type="date" name="audit_to" value="{{ request('audit_to') }}" class="rounded border p-2"></label>
      <button class="rounded bg-slate-800 px-3 py-2 text-white">Filter History</button>
      <a class="rounded border px-3 py-2" href="{{ route('vendor.opening.index', [$business, $branch]) }}">Clear Filters</a>
    </form>
    <div class="mt-3 overflow-x-auto"><table class="w-full min-w-[720px] text-left text-sm">
      <thead><tr class="border-b"><th class="p-2">When</th><th class="p-2">Who</th><th class="p-2">Action</th><th class="p-2">Reason</th><th class="p-2">Before / After</th></tr></thead>
      <tbody>@forelse($audit as $a)
        <tr class="border-b align-top"><td class="p-2">{{ $a->created_at }}</td><td class="p-2">{{ $a->actor_name }} (#{{ $a->actor_user_id }})</td>
          <td class="p-2">{{ $a->action }}</td><td class="p-2">{{ $a->reason }}</td>
          <td class="max-w-xs break-words p-2 text-xs">Before: {{ $a->old_values ?: '-' }}<br>After: {{ $a->new_values ?: '-' }}</td></tr>
      @empty<tr><td colspan="5" class="p-3 text-slate-500">No opening changes yet.</td></tr>@endforelse</tbody>
    </table></div>
    <div class="mt-4">{{ $audit->links() }}</div>
  </section>
</main>
</body>
</html>
