<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Login</title>
    @include('auth.partials.styles')
</head>
<body class="min-h-screen bg-slate-100 flex items-center justify-center p-4">

    <div class="w-full max-w-md rounded-2xl bg-white p-8 shadow-lg">
        <div class="mb-6 text-center">
            <h1 class="text-3xl font-bold text-slate-800">Welcome Back</h1>
            <p class="mt-2 text-sm text-slate-500">
                अपने अकाउंट में लॉगिन करें
            </p>
        </div>

        @if ($errors->any())
            <div class="mb-5 rounded-lg bg-red-50 p-4 text-sm text-red-700">
                <p class="mb-2 font-semibold">Login नहीं हो पाया:</p>
                <ul class="list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
            @csrf

            <div>
                <label for="email" class="mb-1 block text-sm font-medium text-slate-700">
                    Email Address
                </label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    required
                    autocomplete="email"
                    class="w-full rounded-lg border border-slate-300 px-4 py-3 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200"
                    placeholder="you@example.com"
                >
            </div>

            <div>
                <label for="password" class="mb-1 block text-sm font-medium text-slate-700">
                    Password
                </label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    required
                    autocomplete="current-password"
                    class="w-full rounded-lg border border-slate-300 px-4 py-3 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200"
                    placeholder="अपना Password लिखें"
                >
            </div>

            <div class="flex items-center gap-2">
                <input
                    id="remember"
                    name="remember"
                    type="checkbox"
                    value="1"
                    class="h-4 w-4 rounded border-slate-300 text-indigo-600"
                >
                <label for="remember" class="text-sm text-slate-600">
                    मुझे लॉगिन रखें
                </label>
            </div>

            <button
                type="submit"
                class="w-full rounded-lg bg-indigo-600 px-4 py-3 font-semibold text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-400"
            >
                Login
            </button>
        <p class="mt-3 text-center text-sm"><a href="{{ route('password.request') }}" class="font-semibold text-teal-700 underline">Forgot Password?</a></p>
</form>

        <p class="mt-6 text-center text-sm text-slate-600"><a href="{{ route('register') }}" class="font-semibold text-indigo-600 hover:underline">Create a Customer Account</a></p>
    </div>

</body>
</html>
