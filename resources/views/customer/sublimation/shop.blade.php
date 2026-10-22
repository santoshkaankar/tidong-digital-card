<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Personalized Gifts · TIDONG</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900">
<main class="mx-auto max-w-6xl space-y-6 px-4 py-8">
    <header class="flex flex-wrap items-center justify-between gap-3">
        <div><h1 class="text-3xl font-bold">{{ $selectedBusinessName ?: 'Personalized Gifts' }}</h1>
            <p class="text-sm text-slate-600">Custom printing requests, design review and order tracking.</p>
            @if($selectedBusinessId)<a class="text-sm font-semibold text-rose-700 underline" href="{{ route('sublimation.shop') }}">View all studios</a>@endif</div>
        <div class="flex gap-3 text-sm font-semibold">
            <a href="{{ url('/') }}" class="rounded-lg border bg-white px-4 py-2">Home</a>
            @auth<a href="{{ route('customer.sublimation.orders') }}" class="rounded-lg bg-slate-900 px-4 py-2 text-white">My Orders</a>@endauth
        </div>
    </header>
    @if ($errors->any())
        <section role="alert" class="rounded-xl border border-red-200 bg-red-50 p-4 text-red-800">
            <strong>Order not submitted:</strong><ul class="list-inside list-disc">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </section>
    @endif
    <p class="rounded-xl border bg-white p-4 text-sm text-slate-600">No online payment is taken on this page. An order is a request to the studio, subject to design approval and fulfillment confirmation. Delivery charges, if any, must be confirmed separately.</p>
    @if ($products->isEmpty())
        <p class="rounded-xl border bg-white p-6">No active, priced products are available from this studio right now. You can browse other studios.</p>
    @else
        <div class="grid gap-5 md:grid-cols-2">
        @foreach ($products as $product)
            <article class="rounded-2xl border bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-rose-700">{{ $product->category }}</p>
                <h2 class="mt-1 text-xl font-bold">{{ $product->name }}</h2>
                <p class="text-sm text-slate-500">{{ $product->business_name }} · {{ $product->branch_name }}</p>
                <p class="my-3 text-2xl font-semibold">₹{{ number_format($product->sale_price_paise / 100, 2) }} <span class="text-sm font-normal text-slate-500">per piece, before delivery charges</span></p>
                <a href="{{ route('sublimation.products.show', $product->id) }}" class="inline-block rounded-lg bg-rose-700 px-5 py-3 font-semibold text-white">View Product &amp; Customize</a>
            </article>
        @endforeach
        </div>
        <div>{{ $products->withQueryString()->links() }}</div>
    @endif
</main>
</body>
</html>
