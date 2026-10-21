<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Become a Vendor - Digital Platform</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 p-5 text-slate-800">
    <main class="mx-auto max-w-xl rounded-2xl bg-white p-6 shadow-sm sm:p-9">
        <a href="{{ route('customer.home') }}" class="text-sm font-medium text-blue-700">Back to Customer</a>
        <h1 class="mt-5 text-2xl font-bold">Apply to become a Vendor</h1>
        <p class="mt-2 text-sm text-slate-600">Keep your existing login. Your application will be reviewed by the platform team before you can create Businesses.</p>

        @if ($errors->any())
            <div class="mt-5 rounded-lg bg-red-50 p-4 text-sm text-red-800">
                @foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('vendor.apply.store') }}" class="mt-6 space-y-5">
            @csrf
            <div>
                <label for="display_name" class="mb-2 block font-semibold">Vendor / Brand name</label>
                <input id="display_name" name="display_name" value="{{ old('display_name') }}" maxlength="255" minlength="3" required
                       class="w-full rounded-lg border border-slate-300 p-3" placeholder="Your business group name">
            </div>
            <button type="submit" class="rounded-lg bg-blue-700 px-5 py-3 font-semibold text-white">Submit application</button>
        </form>
    </main>
</body>
</html>
