<!DOCTYPE html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>{{ $pageTitle ?? 'Sublimation' }} · TIDONG</title>
@vite(['resources/css/app.css','resources/js/app.js'])
<style>@media print {.no-print{display:none!important} .print-card{border:0!important} body{background:white!important}}</style>
</head><body class="bg-slate-50 text-slate-900">
<div class="mx-auto flex max-w-screen-2xl flex-col items-start gap-5 p-4 xl:flex-row xl:p-6">
@include('vendor.partials.sidebar')
<main class="w-full min-w-0 flex-1 space-y-5">
<header class="no-print flex flex-wrap items-center justify-between gap-3 rounded-2xl bg-slate-950 p-5 text-white">
  <div><p class="text-xs font-semibold uppercase tracking-wide text-rose-300">{{ $business->name }}</p>
    <h1 class="mt-1 text-2xl font-bold">{{ $pageTitle ?? 'Sublimation' }}</h1>
    <p class="mt-1 text-sm text-slate-300">{{ $branch?->name ?? 'No branch created' }}</p></div>
  <nav class="flex flex-wrap gap-2 text-sm">
    <a class="rounded-lg border border-slate-500 px-3 py-2" href="{{ route('vendor.sublimation.index', $business->id) }}">Studio</a>
    <a class="rounded-lg border border-slate-500 px-3 py-2" href="{{ route('vendor.sublimation.products.index', $business->id) }}">Products</a>
    <a class="rounded-lg border border-slate-500 px-3 py-2" href="{{ route('vendor.sublimation.orders.index', $business->id) }}">Orders</a>
  </nav>
</header>
@if (session('success'))<div role="status" class="rounded-xl border border-green-200 bg-green-50 p-4 text-green-800">{{ session('success') }}</div>@endif
@if ($errors->any())
<div role="alert" class="rounded-xl border border-red-200 bg-red-50 p-4 text-red-800"><strong>Please correct:</strong>
<ul class="list-inside list-disc">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
@endif
