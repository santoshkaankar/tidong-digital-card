<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Sublimation Orders · TIDONG</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900">
<main class="mx-auto max-w-4xl space-y-5 px-4 py-8">
    <header class="flex flex-wrap items-center justify-between gap-3">
        <div><h1 class="text-3xl font-bold">My Personalized Gift Orders</h1><p class="text-sm text-slate-500">Only your own orders are shown.</p></div>
        <a href="{{ route('sublimation.shop') }}" class="rounded-lg bg-rose-700 px-4 py-2 text-white">Shop Gifts</a>
    </header>
    @if (session('success'))<p role="status" class="rounded-xl border border-green-200 bg-green-50 p-4 text-green-800">{{ session('success') }}</p>@endif
    @forelse ($orders as $order)
        <article class="rounded-2xl border bg-white p-5">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div><a class="text-xs font-semibold text-blue-700 underline" href="{{ route('customer.sublimation.orders.show', $order->id) }}">{{ $order->order_no }} · View Details</a>
                    <h2 class="text-xl font-bold">{{ $order->product_name_snapshot }} × {{ $order->quantity }}</h2></div>
                <span class="rounded-full bg-slate-100 px-3 py-1 text-sm font-semibold">{{ ucwords(str_replace('_', ' ', $order->status)) }}</span>
            </div>
            <p class="mt-2">Product quote: <strong>₹{{ number_format($order->total_sale_paise / 100, 2) }}</strong></p>
            <p class="text-sm text-slate-500">{{ $order->fulfillment_method === 'delivery' ? 'Delivery requested' : 'Studio pickup' }} · {{ $order->created_at }}</p>
            @if ($order->status === 'approval_pending')<p class="mt-2 rounded-lg bg-amber-50 p-3 text-sm">The studio is waiting for your design approval. Contact the studio to review the proof before printing.</p>@endif
            @if ($order->dispatched_at)
                <p class="mt-2 text-sm">Dispatched: {{ $order->dispatched_at }}</p>
                @if ($order->delivery_carrier)<p class="text-sm">Carrier: {{ $order->delivery_carrier }}</p>@endif
                @if ($order->delivery_tracking)<p class="text-sm">Tracking reference: {{ $order->delivery_tracking }}</p>@endif
            @endif
            @if ($order->delivered_at)<p class="mt-2 text-sm font-semibold text-green-700">Completed: {{ $order->delivered_at }}</p>@endif
            @if ($order->status === 'cancelled')<p class="mt-2 text-sm text-red-700">This order has been cancelled.</p>@endif
        </article>
    @empty
        <p class="rounded-xl border bg-white p-6">You have no Sublimation orders yet.</p>
    @endforelse
    {{ $orders->links() }}
</main>
</body>
</html>
