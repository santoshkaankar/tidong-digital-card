<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Customer Dashboard - Digital Platform</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 p-5 text-slate-800">
    <main class="mx-auto max-w-3xl rounded-2xl bg-white p-6 shadow-sm sm:p-9">
        <h1 class="text-2xl font-bold">Customer Dashboard</h1>
        <p class="mt-2 text-slate-600">Welcome, {{ auth()->user()->name }}.</p>
        <div class="mt-8 rounded-xl border border-slate-200 p-5">
            <h2 class="text-lg font-semibold">Vendor Account</h2>
            <p class="mt-2 text-sm text-slate-600">Run multiple Businesses and Branches using the same login.</p>
            <a href="{{ $hasVendor ? route('vendor.home') : route('vendor.apply') }}"
               class="mt-4 inline-block rounded-lg bg-blue-700 px-5 py-3 font-semibold text-white">
                {{ $hasVendor ? 'Open Vendor Dashboard' : 'Apply to be a Vendor' }}
            </a>
        </div>
        <form method="POST" action="{{ route('logout') }}" class="mt-8">@csrf
            <button type="submit" class="rounded-lg border border-slate-300 px-5 py-2">Logout</button>
        </form>
    </main>
</body>
</html>
