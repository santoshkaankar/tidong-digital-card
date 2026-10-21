
@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page_heading', 'Dashboard Overview')

@section('content')

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-900">
            Welcome, {{ auth()->user()->name }}!
        </h1>
        <p class="mt-2 text-slate-600">
            Your Digital Platform at a glance.
        </p>
    </div>

    <nav class="mb-6 flex flex-wrap gap-3" aria-label="Admin quick actions">
        <a href="{{ route('admin.branch-changes.index') }}" class="rounded-lg bg-blue-700 px-4 py-3 text-sm font-semibold text-white hover:bg-blue-800">Branch Change Approvals</a>
        <a href="{{ route('admin.businesses.index') }}" class="rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-blue-700 hover:bg-slate-50">Business Approvals</a>
        <a href="{{ route('admin.vendors.index') }}" class="rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-blue-700 hover:bg-slate-50">Vendor Management</a>
        <a href="{{ route('admin.global-menu.index') }}" class="rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-blue-700 hover:bg-slate-50">Global Item Master</a>
    </nav>

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-3">

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Total Users</p>
            <p class="mt-3 text-3xl font-bold text-slate-900">
                {{ number_format($stats['users']) }}
            </p>
            <p class="mt-2 text-sm text-slate-500">All registered accounts</p>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Customers</p>
            <p class="mt-3 text-3xl font-bold text-blue-700">
                {{ number_format($stats['customers']) }}
            </p>
            <p class="mt-2 text-sm text-slate-500">Active customer accounts</p>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Vendors</p>
            <p class="mt-3 text-3xl font-bold text-indigo-700">
                {{ number_format($stats['vendors']) }}
            </p>
            <p class="mt-2 text-sm text-slate-500">Vendor profiles</p>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Businesses</p>
            <p class="mt-3 text-3xl font-bold text-emerald-700">
                {{ number_format($stats['businesses']) }}
            </p>
            <p class="mt-2 text-sm text-slate-500">Total business profiles</p>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Branches</p>
            <p class="mt-3 text-3xl font-bold text-violet-700">
                {{ number_format($stats['branches']) }}
            </p>
            <p class="mt-2 text-sm text-slate-500">Total branch locations</p>
        </div>

        <div class="rounded-xl border border-amber-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-amber-700">Pending Vendors</p>
            <p class="mt-3 text-3xl font-bold text-amber-700">
                {{ number_format($stats['pending_vendors']) }}
            </p>
            <p class="mt-2 text-sm text-slate-500">Awaiting vendor approval</p>
        </div>

        <div class="rounded-xl border border-amber-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-amber-700">Pending Businesses</p>
            <p class="mt-3 text-3xl font-bold text-amber-700">
                {{ number_format($stats['pending_businesses']) }}
            </p>
            <p class="mt-2 text-sm text-slate-500">Awaiting business approval</p>
        </div>

    </div>

    <div class="mt-8 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-bold text-slate-900">System Overview</h2>
        <p class="mt-2 text-sm leading-6 text-slate-600">
            Manage users, vendors, businesses, branches, financial modules,
            POS, KDS, delivery, games, and platform security from the
            administration panel.
        </p>
        <p class="mt-3 text-xs text-slate-400">
            Statistics are loaded from the current database.
        </p>
    </div>

@endsection
