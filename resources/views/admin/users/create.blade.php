@extends('layouts.admin')

@section('title', 'Create Admin / Employee')

@section('content')

<div class="mb-6">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">
                Create Admin / Employee
            </h1>

            <p class="mt-1 text-sm text-slate-600">
                Create a controlled platform administrator or employee account.
            </p>
        </div>

        <a
            href="{{ route('admin.users.index') }}"
            class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50"
        >
            Back to Users
        </a>
    </div>
</div>

@if ($errors->any())
    <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4">
        <div class="font-semibold text-red-800">
            Please fix the following errors:
        </div>

        <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-red-700">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="max-w-4xl rounded-xl border border-slate-200 bg-white shadow-sm">

    <form
        method="POST"
        action="{{ route('admin.users.store') }}"
        class="p-6"
    >
        @csrf

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

            {{-- Name --}}
            <div>
                <label
                    for="name"
                    class="mb-2 block text-sm font-medium text-slate-700"
                >
                    Full Name
                </label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name') }}"
                    required
                    maxlength="255"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-slate-500 focus:ring-2 focus:ring-slate-200"
                    placeholder="Enter full name"
                >
            </div>

            {{-- Email --}}
            <div>
                <label
                    for="email"
                    class="mb-2 block text-sm font-medium text-slate-700"
                >
                    Email Address
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    maxlength="255"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-slate-500 focus:ring-2 focus:ring-slate-200"
                    placeholder="name@example.com"
                >
            </div>

            {{-- Password --}}
            <div>
                <label
                    for="password"
                    class="mb-2 block text-sm font-medium text-slate-700"
                >
                    Password
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    minlength="8"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-slate-500 focus:ring-2 focus:ring-slate-200"
                    placeholder="Minimum 8 characters"
                >
            </div>

            {{-- Confirm Password --}}
            <div>
                <label
                    for="password_confirmation"
                    class="mb-2 block text-sm font-medium text-slate-700"
                >
                    Confirm Password
                </label>

                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    required
                    minlength="8"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-slate-500 focus:ring-2 focus:ring-slate-200"
                    placeholder="Re-enter password"
                >
            </div>

            {{-- Account Type --}}
            <div>
                <label
                    for="account_type_id"
                    class="mb-2 block text-sm font-medium text-slate-700"
                >
                    Account Type
                </label>

                <select
                    id="account_type_id"
                    name="account_type_id"
                    required
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none focus:border-slate-500 focus:ring-2 focus:ring-slate-200"
                >
                    <option value="">Select account type</option>

                    @foreach ($accountTypes as $accountType)
                        <option
                            value="{{ $accountType->id }}"
                            @selected(old('account_type_id') == $accountType->id)
                        >
                            {{ $accountType->name }}
                            ({{ $accountType->code }})
                        </option>
                    @endforeach
                </select>

                <p class="mt-1 text-xs text-slate-500">
                    Only controlled Admin/Platform Employee account types are available here.
                </p>
            </div>

            {{-- Role --}}
            <div>
                <label
                    for="role_id"
                    class="mb-2 block text-sm font-medium text-slate-700"
                >
                    Role
                </label>

                <select
                    id="role_id"
                    name="role_id"
                    required
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none focus:border-slate-500 focus:ring-2 focus:ring-slate-200"
                >
                    <option value="">Select role</option>

                    @foreach ($roles as $role)
                        <option
                            value="{{ $role->id }}"
                            @selected(old('role_id') == $role->id)
                        >
                            {{ $role->name }}
                            ({{ $role->code }})
                        </option>
                    @endforeach
                </select>

                <p class="mt-1 text-xs text-slate-500">
                    Super Admin cannot be assigned from this screen.
                </p>
            </div>

            {{-- Scope --}}
            <div>
                <label
                    class="mb-2 block text-sm font-medium text-slate-700"
                >
                    Access Scope
                </label>

                <div class="rounded-lg border border-slate-200 bg-slate-50 px-4 py-3">
                    <div class="font-medium text-slate-800">
                        Platform
                    </div>

                    <div class="mt-1 text-xs text-slate-500">
                        This account will operate at platform level.
                    </div>
                </div>
            </div>

            {{-- Starts At --}}
            <div>
                <label
                    for="starts_at"
                    class="mb-2 block text-sm font-medium text-slate-700"
                >
                    Role Start Date
                </label>

                <input
                    type="datetime-local"
                    id="starts_at"
                    name="starts_at"
                    value="{{ old('starts_at') }}"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-slate-500 focus:ring-2 focus:ring-slate-200"
                >

                <p class="mt-1 text-xs text-slate-500">
                    Leave blank to start immediately.
                </p>
            </div>

            {{-- Expires At --}}
            <div>
                <label
                    for="expires_at"
                    class="mb-2 block text-sm font-medium text-slate-700"
                >
                    Role Expiry Date
                </label>

                <input
                    type="datetime-local"
                    id="expires_at"
                    name="expires_at"
                    value="{{ old('expires_at') }}"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-slate-500 focus:ring-2 focus:ring-slate-200"
                >

                <p class="mt-1 text-xs text-slate-500">
                    Leave blank for no expiry.
                </p>
            </div>

        </div>

        {{-- Information --}}
        <div class="mt-6 rounded-lg border border-blue-200 bg-blue-50 p-4">
            <div class="font-semibold text-blue-900">
                Security Notice
            </div>

            <div class="mt-1 text-sm text-blue-800">
                This account will be created by the currently authenticated
                Super Admin. The creation will also be recorded in the audit log.
            </div>
        </div>

        {{-- Buttons --}}
        <div class="mt-6 flex items-center justify-end gap-3">

            <a
                href="{{ route('admin.users.index') }}"
                class="rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50"
            >
                Cancel
            </a>

            <button
                type="submit"
                class="rounded-lg bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-800"
            >
                Create Account
            </button>

        </div>

    </form>

</div>

@endsection