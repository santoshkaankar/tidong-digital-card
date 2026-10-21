
@extends('layouts.admin')

@section('title', 'Users Management')

@section('content')
<div class="w-full min-w-0 space-y-5">

    {{-- Page heading --}}
    <div class="flex min-w-0 flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="min-w-0">
            <p class="text-sm text-slate-500">Admin / Users Management</p>
            <h1 class="mt-1 text-2xl font-bold text-slate-900">Users Management</h1>
            <p class="mt-1 text-sm text-slate-500">
                Manage user accounts, account types and assigned roles.
            </p>
        </div>

        <a href="{{ route('admin.users.create') }}"
           class="inline-flex w-full shrink-0 items-center justify-center rounded-lg bg-slate-900 px-4 py-3 text-sm font-semibold text-white hover:bg-slate-800 sm:w-auto">
            + Create Admin / Employee
        </a>
    </div>

    {{-- Notifications --}}
    @if(session('success'))
        <div class="break-words rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="break-words rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    {{-- Search --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
        <form method="GET"
              action="{{ route('admin.users.index') }}"
              class="flex min-w-0 flex-col gap-3 sm:flex-row sm:items-end">

            <div class="min-w-0 flex-1">
                <label for="search" class="mb-2 block text-sm font-medium text-slate-700">
                    Search Users
                </label>

                <input id="search"
                       type="text"
                       name="search"
                       value="{{ $search }}"
                       placeholder="Search by name or email..."
                       class="w-full min-w-0 rounded-lg border border-slate-300 px-4 py-3 text-sm focus:border-slate-500 focus:outline-none focus:ring-2 focus:ring-slate-200">
            </div>

            <div class="flex gap-2">
                <button type="submit"
                        class="flex-1 rounded-lg bg-slate-900 px-5 py-3 text-sm font-semibold text-white hover:bg-slate-800 sm:flex-none">
                    Search
                </button>

                @if($search !== '')
                    <a href="{{ route('admin.users.index') }}"
                       class="flex-1 rounded-lg border border-slate-300 px-4 py-3 text-center text-sm font-semibold text-slate-700 hover:bg-slate-50 sm:flex-none">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Users section --}}
    <div class="min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

        <div class="border-b border-slate-200 px-4 py-4 sm:px-5">
            <h2 class="font-semibold text-slate-900">All Users</h2>
            <p class="mt-1 text-xs text-slate-500">
                Showing {{ $users->count() }} users on this page out of {{ $users->total() }} total records.
            </p>
        </div>

        {{-- MOBILE: user cards --}}
        <div class="space-y-3 p-3 md:hidden">

            @forelse($users as $user)

                @php
                    $hasActiveAccountType = $user->userAccountTypes
                        ->contains(fn ($item) => $item->status === 'active');

                    $hasSuperAdminRole = $user->roleAssignments
                        ->contains(function ($assignment) {
                            return $assignment->is_active
                                && $assignment->scope_type === 'platform'
                                && $assignment->scope_id === null
                                && $assignment->role
                                && $assignment->role->code === 'super_admin'
                                && (!$assignment->starts_at || \Illuminate\Support\Carbon::parse($assignment->starts_at)->lte(now()))
                                && (!$assignment->expires_at || \Illuminate\Support\Carbon::parse($assignment->expires_at)->gt(now()));
                        });

                    $canManageAccount = $user->userAccountTypes
                        ->contains(function ($item) {
                            return in_array($item->accountType?->code, ['admin', 'platform_employee'], true);
                        });

                    $activeAssignments = $user->roleAssignments
                        ->filter(function ($assignment) {
                            return $assignment->is_active
                                && (!$assignment->starts_at || \Illuminate\Support\Carbon::parse($assignment->starts_at)->lte(now()))
                                && (!$assignment->expires_at || \Illuminate\Support\Carbon::parse($assignment->expires_at)->gt(now()));
                        });
                @endphp

                <article class="min-w-0 rounded-xl border border-slate-200 p-4">

                    <div class="flex min-w-0 items-start gap-3">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-slate-100 text-sm font-bold text-slate-700">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>

                        <div class="min-w-0 flex-1">
                            <h3 class="break-words font-semibold text-slate-900">
                                {{ $user->name }}
                            </h3>
                            <p class="mt-1 break-all text-sm text-slate-500">
                                {{ $user->email }}
                            </p>
                            <p class="mt-1 text-xs text-slate-400">
                                User #{{ $user->id }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-4 space-y-3 border-t border-slate-100 pt-3">

                        <div>
                            <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Account Types
                            </p>
                            <div class="flex flex-wrap gap-1.5">
                                @forelse($user->userAccountTypes as $item)
                                    <span class="max-w-full break-words rounded-full px-2.5 py-1 text-xs font-medium {{ $item->status === 'active' ? 'bg-blue-50 text-blue-700' : 'bg-slate-100 text-slate-500' }}">
                                        {{ $item->accountType?->name ?? 'Unknown' }}
                                        @if($item->status !== 'active')
                                            (Inactive)
                                        @endif
                                    </span>
                                @empty
                                    <span class="text-sm text-slate-400">None</span>
                                @endforelse
                            </div>
                        </div>

                        <div>
                            <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Roles
                            </p>
                            @forelse($activeAssignments as $assignment)
                                <p class="break-words text-sm text-slate-700">
                                    {{ $assignment->role?->name ?? 'Unknown Role' }}
                                    <span class="text-xs text-slate-400">
                                        ({{ ucfirst($assignment->scope_type) }} scope)
                                    </span>
                                </p>
                            @empty
                                <p class="text-sm text-slate-400">No active roles</p>
                            @endforelse
                        </div>

                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <span class="rounded-full px-3 py-1 text-xs font-semibold
                                {{ $hasSuperAdminRole ? 'bg-purple-100 text-purple-700' : ($hasActiveAccountType ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700') }}">
                                {{ $hasSuperAdminRole ? 'Super Admin' : ($hasActiveAccountType ? 'Active' : 'Inactive') }}
                            </span>

                            <span class="text-xs text-slate-500">
                                {{ $user->created_at?->format('d M Y, h:i A') ?? '-' }}
                            </span>
                        </div>
                    </div>

                    <div class="mt-4 flex flex-wrap gap-2 border-t border-slate-100 pt-3">
                        <a href="{{ route('admin.users.show', $user) }}"
                           class="inline-flex min-w-[90px] flex-1 items-center justify-center rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                            View
                        </a>

                        @if($canManageAccount && !$hasSuperAdminRole)
                            <a href="{{ route('admin.users.edit', $user) }}"
                               class="inline-flex min-w-[90px] flex-1 items-center justify-center rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">
                                Edit
                            </a>
                        @endif
                    </div>

                </article>

            @empty
                <div class="px-4 py-10 text-center">
                    <p class="font-semibold text-slate-700">No users found</p>
                    <p class="mt-1 text-sm text-slate-500">Try another search.</p>
                </div>
            @endforelse
        </div>

        {{-- DESKTOP: six-column table --}}
        <div class="hidden min-w-0 overflow-x-auto md:block">
            <table class="w-full min-w-[950px] table-auto text-left text-sm">
                <thead class="bg-slate-50">
                    <tr class="border-b border-slate-200 text-xs uppercase tracking-wide text-slate-500">
                        <th class="px-4 py-4">User</th>
                        <th class="px-4 py-4">Account Types</th>
                        <th class="px-4 py-4">Roles</th>
                        <th class="px-4 py-4">Status</th>
                        <th class="whitespace-nowrap px-4 py-4">Registered</th>
                        <th class="whitespace-nowrap px-4 py-4 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $user)

                        @php
                            $hasActiveAccountType = $user->userAccountTypes
                                ->contains(fn ($item) => $item->status === 'active');

                            $hasSuperAdminRole = $user->roleAssignments
                                ->contains(function ($assignment) {
                                    return $assignment->is_active
                                        && $assignment->scope_type === 'platform'
                                        && $assignment->scope_id === null
                                        && $assignment->role
                                        && $assignment->role->code === 'super_admin'
                                        && (!$assignment->starts_at || \Illuminate\Support\Carbon::parse($assignment->starts_at)->lte(now()))
                                        && (!$assignment->expires_at || \Illuminate\Support\Carbon::parse($assignment->expires_at)->gt(now()));
                                });

                            $canManageAccount = $user->userAccountTypes
                                ->contains(function ($item) {
                                    return in_array($item->accountType?->code, ['admin', 'platform_employee'], true);
                                });

                            $activeAssignments = $user->roleAssignments
                                ->filter(function ($assignment) {
                                    return $assignment->is_active
                                        && (!$assignment->starts_at || \Illuminate\Support\Carbon::parse($assignment->starts_at)->lte(now()))
                                        && (!$assignment->expires_at || \Illuminate\Support\Carbon::parse($assignment->expires_at)->gt(now()));
                                });
                        @endphp

                        <tr class="align-top hover:bg-slate-50">
                            <td class="max-w-[240px] break-words px-4 py-4">
                                <div class="font-semibold text-slate-900">{{ $user->name }}</div>
                                <div class="mt-1 break-all text-xs text-slate-500">{{ $user->email }}</div>
                                <div class="mt-1 text-xs text-slate-400">User #{{ $user->id }}</div>
                            </td>

                            <td class="max-w-[200px] px-4 py-4">
                                <div class="flex flex-wrap gap-1.5">
                                    @forelse($user->userAccountTypes as $item)
                                        <span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $item->status === 'active' ? 'bg-blue-50 text-blue-700' : 'bg-slate-100 text-slate-500' }}">
                                            {{ $item->accountType?->name ?? 'Unknown' }}
                                            @if($item->status !== 'active')(Inactive)@endif
                                        </span>
                                    @empty
                                        <span class="text-xs text-slate-400">None</span>
                                    @endforelse
                                </div>
                            </td>

                            <td class="max-w-[200px] px-4 py-4">
                                @forelse($activeAssignments as $assignment)
                                    <div class="mb-2">
                                        <div class="font-medium text-slate-700">{{ $assignment->role?->name ?? 'Unknown Role' }}</div>
                                        <div class="text-xs text-slate-400">{{ ucfirst($assignment->scope_type) }} scope</div>
                                    </div>
                                @empty
                                    <span class="text-xs text-slate-400">No active roles</span>
                                @endforelse
                            </td>

                            <td class="whitespace-nowrap px-4 py-4">
                                @if($hasSuperAdminRole)
                                    <span class="rounded-full bg-purple-100 px-3 py-1 text-xs font-semibold text-purple-700">Super Admin</span>
                                @elseif($hasActiveAccountType)
                                    <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">Active</span>
                                @else
                                    <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">Inactive</span>
                                @endif
                            </td>

                            <td class="whitespace-nowrap px-4 py-4 text-slate-600">
                                {{ $user->created_at?->format('d M Y') ?? '-' }}
                                <div class="mt-1 text-xs text-slate-400">{{ $user->created_at?->format('h:i A') ?? '' }}</div>
                            </td>

                            <td class="whitespace-nowrap px-4 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.users.show', $user) }}"
                                       class="rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                                        View
                                    </a>

                                    @if($canManageAccount && !$hasSuperAdminRole)
                                        <a href="{{ route('admin.users.edit', $user) }}"
                                           class="rounded-lg bg-slate-900 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-800">
                                            Edit
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-slate-500">
                                No users found. Try another search.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($users->hasPages())
            <div class="border-t border-slate-200 px-3 py-4 sm:px-5">
                {{ $users->onEachSide(1)->links() }}
            </div>
        @endif
    </div>
</div>
@endsection