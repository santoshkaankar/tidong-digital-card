@extends(request()->routeIs('admin.*') ? 'layouts.admin' : 'layouts.platform-ops')
@section('title','Delivery Technical Diagnostics')
@section('page_heading','Delivery Technical Diagnostics')
@section('content')
<div class="max-w-4xl space-y-5">
  <h1 class="text-xl font-bold">Delivery Module — Technical Status</h1>
@if(app(\App\Services\Platform\PlatformCapability::class)->allows(auth()->user(),'delivery.partners.approve'))
<a class="inline-flex rounded-lg bg-blue-700 px-4 py-2 text-white" href="{{ route(explode('.',request()->route()->getName())[0].'.delivery-partner-approvals.index') }}">Review Delivery Partner Approvals</a>
@endif
  <p class="rounded border border-slate-300 bg-white p-4 text-sm">Website staff can monitor aggregate availability and technical health only. Platform staff can approve identity only with permission; Vendor approves its own business roster. Dispatch, cancellations, fees and customer records remain business-controlled.</p>
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
