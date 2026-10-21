@extends(request()->routeIs('admin.*') ? 'layouts.admin' : 'layouts.platform-ops')
@section('title','Delivery Technical Diagnostics')
@section('page_heading','Delivery Technical Diagnostics')
@section('content')
<div class="max-w-4xl space-y-5">
  <h1 class="text-xl font-bold">Delivery Module — Technical Status</h1>
  <p class="rounded border border-slate-300 bg-white p-4 text-sm">Website staff can monitor aggregate availability and technical health only. Partner approvals, customer records, dispatch, cancellations, fees and firm decisions belong to the Vendor or its authorized business employees.</p>
  <div class="grid gap-4 md:grid-cols-2">
    <section class="rounded bg-white p-4 shadow"><h2 class="font-semibold">Jobs by Status (Counts Only)</h2>
      @forelse($counts as $item)<p class="py-1">{{ ucfirst(str_replace('_',' ',$item->status)) }}: {{ $item->total }}</p>@empty<p>No jobs recorded.</p>@endforelse
    </section>
    <section class="rounded bg-white p-4 shadow"><h2 class="font-semibold">Partners by Status (Counts Only)</h2>
      @forelse($partnerCounts as $item)<p class="py-1">{{ ucfirst($item->status) }}: {{ $item->total }}</p>@empty<p>No partners recorded.</p>@endforelse
    </section>
  </div>
</div>
@endsection
