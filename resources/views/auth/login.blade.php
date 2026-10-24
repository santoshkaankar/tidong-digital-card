<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TIDONG | Sign In</title>
    @include('auth.partials.styles')
</head>
<body class="min-h-screen bg-slate-100 flex items-center justify-center p-4">

    <div class="w-full max-w-md rounded-2xl bg-white p-5 shadow-lg sm:p-8">
        <div class="mb-6 text-center">
            <a href="{{ url('/') }}" class="mb-5 inline-flex items-center justify-center rounded-lg bg-white p-2" aria-label="TIDONG home"><img src="{{ asset('assets/tidong-full.jpeg') }}" class="h-12 w-auto max-w-full object-contain" alt="TIDONG"></a>
            <h1 class="text-3xl font-bold text-slate-800">Welcome Back</h1>
            <p class="mt-2 text-sm text-slate-500">
                Sign in to your TIDONG account
            </p>
        </div>

        @if ($errors->any())
            <div class="mb-5 rounded-lg bg-red-50 p-4 text-sm text-red-700">
                <p class="mb-2 font-semibold">Sign in was unsuccessful:</p>
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
                    placeholder="Enter your password"
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
                    Remember me
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

        @if(app(\App\Services\Platform\ModuleRegistry::class)->isEnabled('public_registration'))
            <p class="mt-6 text-center text-sm text-slate-600"><a href="{{ route('register') }}" class="font-semibold text-indigo-600 hover:underline">Create an account</a></p>
        @else
            <p class="mt-6 text-center text-sm text-slate-500">New account registration is currently unavailable.</p>
        @endif
    </div>

</body>
</html>
