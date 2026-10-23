<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Forgot Password · TIDONG</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 p-4">
<main class="mx-auto mt-14 max-w-md rounded-2xl bg-white p-6 shadow-md sm:p-8">
    <h1 class="text-2xl font-bold">Forgot Password</h1>
    <p class="mt-2 text-sm text-slate-600">Enter your registered email address to request a reset link.</p>

    @if(session('success'))
        <p role="status" class="mt-3 rounded-lg bg-emerald-50 p-3">{{ session('success') }}</p>
    @endif

    @if($errors->any())
        <p role="alert" class="mt-3 text-red-700">{{ $errors->first() }}</p>
    @endif

    <form class="mt-5 space-y-4" method="POST" action="{{ route('password.email') }}">
        @csrf
        <label class="block">Email Address
            <input type="email" name="email" class="mt-1 w-full rounded-lg border p-3"
                   value="{{ old('email') }}" required autocomplete="email">
        </label>
        <button type="submit" class="w-full rounded-lg bg-teal-700 p-3 font-semibold text-white">Send Reset Link</button>
    </form>
    <a class="mt-5 inline-block text-sm text-teal-700 underline" href="{{ route('login') }}">Back to Login</a>
</main>
</body>
</html>
