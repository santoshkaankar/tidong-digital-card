@extends('layouts.admin')

@section('title', 'User Details')

@section('content')
<div class="space-y-6">

    @if(session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">

        <div>
            <div class="text-sm text-slate-500">
                Users Management / User Details
            </div>

            <h1 class="mt-1 text-2xl font-bold text-slate-900">
                {{ $user->name }}
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                User #{{ $user->id }}
            </p>
        </div>

        <div class="flex flex-wrap gap-2">

            <a
                href="{{ route('admin.users.index') }}"
                class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50"
            >
                Back
            </a>

            @if(!$isSuperAdmin)
                <a
                    href="{{ route('admin.users.edit', $user) }}"
                    class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800"
                >
                    Edit User
                </a>
            @endif

        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm lg:col-span-2">

            <div class="flex items-start justify-between gap-4">

                <div>
                    <h2 class="text-lg font-semibold text-slate-900">
                        User Information
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Basic account information.
                    </p>
                </div>

                @if($isSuperAdmin)
                    <span class="rounded-full bg-purple-100 px-3 py-1 text-xs font-semibold text-purple-700">
                        Super Admin
                    </span>
                @elseif($isActive)
                    <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                        Active
                    </span>
                @else
                    <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                        Inactive
                    </span>
                @endif

            </div>

            <div class="mt-6 grid gap-5 md:grid-cols-2">

                <div>
                    <div class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        User ID
                    </div>

                    <div class="mt-1 font-semibold text-slate-900">
                        #{{ $user->id }}
                    </div>
                </div>

                <div>
                    <div class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Registered
                    </div>

                    <div class="mt-1 font-semibold text-slate-900">
                        {{ $user->created_at?->format('d M Y, h:i A') }}
                    </div>
                </div>

                <div>
                    <div class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Name
                    </div>

                    <div class="mt-1 font-semibold text-slate-900">
                        {{ $user->name }}
                    </div>
                </div>

                <div>
                    <div class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Email
                    </div>

                    <div class="mt-1 font-semibold text-slate-900">
                        {{ $user->email }}
                    </div>
                </div>

            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

            <h2 class="text-lg font-semibold text-slate-900">
                Account Control
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Control platform access for this account.
            </p>

            @if($isSuperAdmin)

                <div class="mt-5 rounded-lg border border-purple-200 bg-purple-50 p-4 text-sm text-purple-700">
                    Super Admin protection is enabled. This account cannot be deactivated from the user management screen.
                </div>

            @elseif($isActive)

                <form
                    method="POST"
                    action="{{ route('admin.users.deactivate', $user) }}"
                    class="mt-5"
                    onsubmit="return confirm('Are you sure you want to deactivate this user?');"
                >
                    @csrf

                    <button
                        type="submit"
                        class="w-full rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700"
                    >
                        Deactivate Account
                    </button>
                </form>

            @else

                <form
                    method="POST"
                    action="{{ route('admin.users.activate', $user) }}"
                    class="mt-5"
                >
                    @csrf

                    <button
                        type="submit"
                        class="w-full rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700"
                    >
                        Activate Account
                    </button>
                </form>

            @endif

        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

        <h2 class="text-lg font-semibold text-slate-900">
            Account Types
        </h2>

        <div class="mt-4 space-y-3">

            @forelse($user->userAccountTypes as $accountType)

                <div class="flex flex-col justify-between gap-3 rounded-lg border border-slate-200 p-4 md:flex-row md:items-center">

                    <div>
                        <div class="font-semibold text-slate-900">
                            {{ $accountType->accountType?->name ?? 'Unknown Account Type' }}
                        </div>

                        <div class="mt-1 text-xs text-slate-500">
                            Code:
                            {{ $accountType->accountType?->code ?? '-' }}
                        </div>
                    </div>

                    @if($accountType->status === 'active')
                        <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                            Active
                        </span>
                    @else
                        <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                            Inactive
                        </span>
                    @endif

                </div>

            @empty

                <div class="rounded-lg bg-slate-50 p-4 text-sm text-slate-500">
                    No account types assigned.
                </div>

            @endforelse

        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

        <h2 class="text-lg font-semibold text-slate-900">
            Assigned Roles
        </h2>

        <div class="mt-4 overflow-x-auto">

            <table class="min-w-full text-left text-sm">

                <thead>
                    <tr class="border-b border-slate-200 text-xs uppercase tracking-wide text-slate-400">
                        <th class="px-3 py-3">Role</th>
                        <th class="px-3 py-3">Scope</th>
                        <th class="px-3 py-3">Start</th>
                        <th class="px-3 py-3">Expiry</th>
                        <th class="px-3 py-3">Status</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($user->roleAssignments as $assignment)

                        <tr class="border-b border-slate-100">

                            <td class="px-3 py-3 font-semibold text-slate-900">
                                {{ $assignment->role?->name ?? 'Unknown Role' }}
                            </td>

                            <td class="px-3 py-3 text-slate-600">
                                {{ ucfirst($assignment->scope_type) }}
                            </td>

                            <td class="px-3 py-3 text-slate-600">
                                {{ $assignment->starts_at ? \Illuminate\Support\Carbon::parse($assignment->starts_at)->format('d M Y') : '-' }}
                            </td>

                            <td class="px-3 py-3 text-slate-600">
                                {{ $assignment->expires_at ? \Illuminate\Support\Carbon::parse($assignment->expires_at)->format('d M Y') : 'No expiry' }}
                            </td>

                            <td class="px-3 py-3">

                                @if($assignment->is_active)
                                    <span class="rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700">
                                        Active
                                    </span>
                                @else
                                    <span class="rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700">
                                        Inactive
                                    </span>
                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td
                                colspan="5"
                                class="px-3 py-6 text-center text-sm text-slate-500"
                            >
                                No roles assigned.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

        <h2 class="text-lg font-semibold text-slate-900">
            Audit History
        </h2>

        <div class="mt-4 space-y-4">

            @forelse($auditLogs as $log)

                <div class="rounded-lg border border-slate-200 p-4">

                    <div class="flex flex-col justify-between gap-2 md:flex-row">

                        <div>
                            <div class="font-semibold text-slate-900">
                                {{ $log->action }}
                            </div>

                            <div class="mt-1 text-sm text-slate-500">
                                {{ $log->reason }}
                            </div>
                        </div>

                        <div class="text-xs text-slate-400">
                            {{ \Illuminate\Support\Carbon::parse($log->created_at)->format('d M Y, h:i A') }}
                        </div>

                    </div>

                    <div class="mt-2 text-xs text-slate-400">
                        Actor User #{{ $log->actor_user_id }}
                    </div>

                </div>

            @empty

                <div class="rounded-lg bg-slate-50 p-4 text-sm text-slate-500">
                    No audit history found.
                </div>

            @endforelse

        </div>
    </div>

</div>
@endsection