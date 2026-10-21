<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>@yield('title','Platform Operations') - TIDONG</title>@vite(['resources/css/app.css','resources/js/app.js'])</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
<header class="bg-slate-900 text-white p-4"><div class="mx-auto max-w-6xl flex flex-wrap justify-between gap-3"><strong>@yield('page_heading','Platform Operations')</strong><div class="flex gap-4"><a href="{{ route(request()->routeIs('employee.*') ? 'employee.home' : 'staff-admin.home') }}">Dashboard</a><form action="{{ route('logout') }}" method="POST">@csrf<button type="submit">Logout</button></form></div></div></header>
<main class="mx-auto max-w-6xl p-4">@yield('content')</main></body></html>
