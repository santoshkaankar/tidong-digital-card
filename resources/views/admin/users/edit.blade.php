@extends('layouts.admin')

@section('title', 'Edit User')

@section('content')
<div class="space-y-6">

    <div>
        <div class="text-sm text-slate-500">
            Users Management / Edit User
        </div>

        <h1 class="mt-1 text-2xl font-bold text-slate-900">
            Edit User
        </h1>

        <p class="mt-1 text-sm text-slate-500">
            Update user profile, account type and platform role.
        </p>
    </div>

    @if($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <div class="font-semibold">Please fix the following:</div>

            <ul class="mt-2 list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    @if($isSuperAdmin)
        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
            This account is a Super Admin. Super Admin accounts cannot be modified from this screen.
        </div>
    @endif

    <form
        method="POST"
        action="{{ route('admin.users.update', $user) }}"
        class="space-y-6"
    >
        @csrf
        @method('PUT')

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

            <h2 class="text-lg font-semibold text-slate-900">
                Basic Information
            </h2>

            <div class="mt-5 grid gap-5 md:grid-cols-2">

                <div>
                    <label class="text-sm font-medium text-slate-700">
                        Full Name
                    </label>

                    <input
                        type="text"
                        name="name"
                        value="{{ old('name', $user->name) }}"
                        required
                        @disabled($isSuperAdmin)
                        class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none disabled:bg-slate-100"
                    >
                </div>

                <div>
                    <label class="text-sm font-medium text-slate-700">
                        Email
                    </label>

                    <input
                        type="email"
                        name="email"
                        value="{{ old('email', $user->email) }}"
                        required
                        @disabled($isSuperAdmin)
                        class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none disabled:bg-slate-100"
                    >
                </div>

            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

            <h2 class="text-lg font-semibold text-slate-900">
                Access & Authorization
            </h2>

            <div class="mt-5 grid gap-5 md:grid-cols-2">

                <div>
                    <label class="text-sm font-medium text-slate-700">
                        Account Type
                    </label>

                    <select
                        name="account_type_id"
                        required
                        @disabled($isSuperAdmin)
                        class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none disabled:bg-slate-100"
                    >
                        @foreach($accountTypes as $accountType)
                            <option
                                value="{{ $accountType->id }}"
                                @selected(
                                    old(
                                        'account_type_id',
                                        $currentAccountType?->account_type_id
                                    ) == $accountType->id
                                )
                            >
                                {{ $accountType->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-sm font-medium text-slate-700">
                        Platform Role
                    </label>

                    <select
                        name="role_id"
                        required
                        @disabled($isSuperAdmin)
                        class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none disabled:bg-slate-100"
                    >
                        @foreach($roles as $role)
                            <option
                                value="{{ $role->id }}"
                                @selected(
                                    old(
                                        'role_id',
                                        $currentRoleAssignment?->role_id
                                    ) == $role->id
                                )
                            >
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-sm font-medium text-slate-700">
                        Role Start Date
                    </label>

                    <input
                        type="date"
                        name="starts_at"
                        value="{{ old(
                            'starts_at',
                            $currentRoleAssignment?->starts_at
                                ? \Illuminate\Support\Carbon::parse($currentRoleAssignment->starts_at)->format('Y-m-d')
                                : ''
                        ) }}"
                        @disabled($isSuperAdmin)
                        class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none disabled:bg-slate-100"
                    >
                </div>

                <div>
                    <label class="text-sm font-medium text-slate-700">
                        Role Expiry Date
                    </label>

                    <input
                        type="date"
                        name="expires_at"
                        value="{{ old(
                            'expires_at',
                            $currentRoleAssignment?->expires_at
                                ? \Illuminate\Support\Carbon::parse($currentRoleAssignment->expires_at)->format('Y-m-d')
                                : ''
                        ) }}"
                        @disabled($isSuperAdmin)
                        class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none disabled:bg-slate-100"
                    >
                </div>

            </div>

            <div class="mt-5 rounded-lg bg-slate-50 p-4 text-sm text-slate-600">
                <strong>Access Scope:</strong> Platform
            </div>
        </div>

        <div class="flex items-center justify-between">

            <a
                href="{{ route('admin.users.show', $user) }}"
                class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50"
            >
                Cancel
            </a>

            @unless($isSuperAdmin)
                <button
                    type="submit"
                    class="rounded-lg bg-slate-900 px-5 py-2 text-sm font-semibold text-white hover:bg-slate-800"
                >
                    Save Changes
                </button>
            @endunless

        </div>

    </form>

</div>
@endsection