<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Reset Password · TIDONG</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 p-4">
<main class="mx-auto mt-14 max-w-md rounded-2xl bg-white p-6 shadow-md sm:p-8">
    <h1 class="text-2xl font-bold">Reset Password</h1>

    @if($errors->any())
        <p role="alert" class="mt-3 text-red-700">{{ $errors->first() }}</p>
    @endif

    <form method="POST" class="mt-5 space-y-4" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <label class="block">Email Address
            <input name="email" type="email" class="mt-1 w-full rounded-lg border p-3"
                   value="{{ old('email', $email) }}" required>
        </label>
        <label class="block">New Password
            <input name="password" type="password" class="mt-1 w-full rounded-lg border p-3"
                   minlength="8" required autocomplete="new-password">
        </label>
        <label class="block">Confirm Password
            <input name="password_confirmation" type="password" class="mt-1 w-full rounded-lg border p-3"
                   minlength="8" required autocomplete="new-password">
        </label>
        <button type="submit" class="w-full rounded-lg bg-teal-700 p-3 font-semibold text-white">Reset Password</button>
    </form>
</main>
</body>
</html>
