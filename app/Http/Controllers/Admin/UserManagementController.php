<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    /**
     * Admin/Employee accounts ki list.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $users = User::query()
            ->with([
                'userAccountTypes.accountType',
                'roleAssignments.role',
            ])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'search'));
    }

    /**
     * Naya Admin/Employee form.
     */
    public function create()
    {
        $accountTypes = DB::table('account_types')
            ->where('is_active', true)
            ->whereIn('code', ['admin', 'platform_employee'])
            ->orderBy('name')
            ->get();

        $roles = DB::table('roles')
            ->where('code', '!=', 'super_admin')
            ->orderBy('name')
            ->get();

        return view('admin.users.create', compact('accountTypes', 'roles'));
    }

    /**
     * Naya Admin/Employee account create karein.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],

            'account_type_id' => [
                'required',
                'integer',
                Rule::exists('account_types', 'id')
                    ->where(fn ($query) => $query
                        ->where('is_active', true)
                        ->whereIn('code', ['admin', 'platform_employee'])),
            ],

            'role_id' => [
                'required',
                'integer',
                Rule::exists('roles', 'id')
                    ->where('code', '!=', 'super_admin'),
            ],

            'starts_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after:starts_at'],
        ]);

        $creator = $request->user();

        DB::transaction(function () use ($validated, $creator) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            DB::table('user_account_types')->insert([
                'user_id' => $user->id,
                'account_type_id' => $validated['account_type_id'],
                'status' => 'active',
                'activated_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('user_role_assignments')->insert([
                'user_id' => $user->id,
                'role_id' => $validated['role_id'],
                'scope_type' => 'platform',
                'scope_id' => null,
                'assigned_by' => $creator->id,
                'starts_at' => $validated['starts_at'] ?? now(),
                'expires_at' => $validated['expires_at'] ?? null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->writeAuditLog(
                $creator->id,
                'user.created',
                $user->id,
                null,
                [
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'account_type_id' => $validated['account_type_id'],
                    'role_id' => $validated['role_id'],
                    'scope_type' => 'platform',
                    'scope_id' => null,
                ],
                'Admin/Employee account created from Super Admin panel.',
                'admin.users.create'
            );
        });

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Admin/Employee account successfully created.');
    }

    /**
     * Admin/Employee ki details aur audit history.
     */
    public function show(User $user)
    {
        $this->ensureManagedAccount($user);

        $user->load([
            'userAccountTypes.accountType',
            'roleAssignments.role',
        ]);

        $auditLogs = DB::table('audit_logs')
            ->where('entity_type', 'User')
            ->where('entity_id', $user->id)
            ->orderByDesc('id')
            ->limit(50)
            ->get();

        $isSuperAdmin = $this->isSuperAdminAccount($user);

        $isActive = $user->userAccountTypes()
            ->where('status', 'active')
            ->whereHas('accountType', function ($query) {
                $query->whereIn('code', ['admin', 'platform_employee']);
            })
            ->exists();

        return view('admin.users.show', compact(
            'user',
            'auditLogs',
            'isSuperAdmin',
            'isActive'
        ));
    }

    /**
     * Edit form.
     */
    public function edit(User $user)
    {
        $this->ensureManagedAccount($user);

        if ($this->isSuperAdminAccount($user)) {
            abort(403, 'Super Admin account cannot be edited here.');
        }

        $user->load([
            'userAccountTypes.accountType',
            'roleAssignments.role',
        ]);

        $accountTypes = DB::table('account_types')
            ->where('is_active', true)
            ->whereIn('code', ['admin', 'platform_employee'])
            ->orderBy('name')
            ->get();

        $roles = DB::table('roles')
            ->where('code', '!=', 'super_admin')
            ->orderBy('name')
            ->get();

        $currentAccountType = $user->userAccountTypes()
            ->whereHas('accountType', function ($query) {
                $query->whereIn('code', ['admin', 'platform_employee']);
            })
            ->first();

        $currentRoleAssignment = $user->roleAssignments()
            ->where('scope_type', 'platform')
            ->whereNull('scope_id')
            ->whereHas('role', function ($query) {
                $query->where('code', '!=', 'super_admin');
            })
            ->orderByDesc('id')
            ->first();

        $isSuperAdmin = $this->isSuperAdminAccount($user);

        return view('admin.users.edit', compact(
            'user',
            'accountTypes',
            'roles',
            'currentAccountType',
            'currentRoleAssignment',
            'isSuperAdmin'
        ));
    }

    /**
     * Admin/Employee details update.
     */
    public function update(Request $request, User $user)
    {
        $this->ensureManagedAccount($user);

        if ($this->isSuperAdminAccount($user)) {
            return redirect()
                ->route('admin.users.show', $user)
                ->with('error', 'Super Admin account cannot be modified.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],

            'account_type_id' => [
                'required',
                'integer',
                Rule::exists('account_types', 'id')
                    ->where(fn ($query) => $query
                        ->where('is_active', true)
                        ->whereIn('code', ['admin', 'platform_employee'])),
            ],

            'role_id' => [
                'required',
                'integer',
                Rule::exists('roles', 'id')
                    ->where('code', '!=', 'super_admin'),
            ],

            'starts_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after:starts_at'],
        ]);

        $actor = $request->user();

        DB::transaction(function () use ($validated, $user, $actor) {
            // Update se pehle purani details save karein.
            $oldName = $user->name;
            $oldEmail = $user->email;

            $oldAccountType = DB::table('user_account_types')
                ->join(
                    'account_types',
                    'account_types.id',
                    '=',
                    'user_account_types.account_type_id'
                )
                ->where('user_account_types.user_id', $user->id)
                ->whereIn('account_types.code', ['admin', 'platform_employee'])
                ->select(
                    'user_account_types.*',
                    'account_types.code as account_type_code',
                    'account_types.name as account_type_name'
                )
                ->first();

            $oldRole = DB::table('user_role_assignments')
                ->join(
                    'roles',
                    'roles.id',
                    '=',
                    'user_role_assignments.role_id'
                )
                ->where('user_role_assignments.user_id', $user->id)
                ->where('user_role_assignments.scope_type', 'platform')
                ->whereNull('user_role_assignments.scope_id')
                ->where('roles.code', '!=', 'super_admin')
                ->orderByDesc('user_role_assignments.id')
                ->select(
                    'user_role_assignments.*',
                    'roles.code as role_code',
                    'roles.name as role_name'
                )
                ->first();

            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
            ]);

            if ($oldAccountType) {
                DB::table('user_account_types')
                    ->where('id', $oldAccountType->id)
                    ->update([
                        'account_type_id' => $validated['account_type_id'],
                        'status' => 'active',
                        'activated_at' => now(),
                        'updated_at' => now(),
                    ]);
            } else {
                DB::table('user_account_types')->insert([
                    'user_id' => $user->id,
                    'account_type_id' => $validated['account_type_id'],
                    'status' => 'active',
                    'activated_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            if ($oldRole) {
                DB::table('user_role_assignments')
                    ->where('id', $oldRole->id)
                    ->update([
                        'role_id' => $validated['role_id'],
                        'assigned_by' => $actor->id,
                        'starts_at' => $validated['starts_at'] ?? now(),
                        'expires_at' => $validated['expires_at'] ?? null,
                        'is_active' => true,
                        'updated_at' => now(),
                    ]);
            } else {
                DB::table('user_role_assignments')->insert([
                    'user_id' => $user->id,
                    'role_id' => $validated['role_id'],
                    'scope_type' => 'platform',
                    'scope_id' => null,
                    'assigned_by' => $actor->id,
                    'starts_at' => $validated['starts_at'] ?? now(),
                    'expires_at' => $validated['expires_at'] ?? null,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $accountTypeName = DB::table('account_types')
                ->where('id', $validated['account_type_id'])
                ->value('name');

            $roleName = DB::table('roles')
                ->where('id', $validated['role_id'])
                ->value('name');

            $this->writeAuditLog(
                $actor->id,
                'user.updated',
                $user->id,
                [
                    'name' => $oldName,
                    'email' => $oldEmail,
                    'account_type' => $oldAccountType?->account_type_name,
                    'role' => $oldRole?->role_name,
                ],
                [
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'account_type' => $accountTypeName,
                    'role' => $roleName,
                    'starts_at' => $validated['starts_at'] ?? null,
                    'expires_at' => $validated['expires_at'] ?? null,
                ],
                'User account details, account type or role updated from Super Admin panel.',
                'admin.users.edit'
            );
        });

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'User successfully updated.');
    }

    /**
     * Account deactivate.
     */
    public function deactivate(Request $request, User $user)
    {
        $this->ensureManagedAccount($user);

        if ($this->isSuperAdminAccount($user)) {
            return redirect()
                ->route('admin.users.show', $user)
                ->with('error', 'Super Admin cannot be deactivated.');
        }

        $actor = $request->user();

        DB::transaction(function () use ($user, $actor) {
            DB::table('user_account_types')
                ->where('user_id', $user->id)
                ->whereIn('account_type_id', function ($query) {
                    $query->select('id')
                        ->from('account_types')
                        ->whereIn('code', ['admin', 'platform_employee']);
                })
                ->update([
                    'status' => 'inactive',
                    'updated_at' => now(),
                ]);

            DB::table('user_role_assignments')
                ->where('user_id', $user->id)
                ->where('scope_type', 'platform')
                ->whereNull('scope_id')
                ->whereHas('role', function ($query) {
                    // Super Admin role ko kabhi deactivate nahi karna.
                    $query->where('code', '!=', 'super_admin');
                })
                ->update([
                    'is_active' => false,
                    'updated_at' => now(),
                ]);

            $this->writeAuditLog(
                $actor->id,
                'user.deactivated',
                $user->id,
                ['status' => 'active'],
                ['status' => 'inactive'],
                'Admin/Employee account deactivated from Super Admin panel.',
                'admin.users.deactivate'
            );
        });

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'User account deactivated successfully.');
    }

    /**
     * Account activate.
     */
    public function activate(Request $request, User $user)
    {
        $this->ensureManagedAccount($user);

        if ($this->isSuperAdminAccount($user)) {
            return redirect()
                ->route('admin.users.show', $user)
                ->with('error', 'Super Admin account does not require activation.');
        }

        $actor = $request->user();

        DB::transaction(function () use ($user, $actor) {
            $accountType = DB::table('user_account_types')
                ->join(
                    'account_types',
                    'account_types.id',
                    '=',
                    'user_account_types.account_type_id'
                )
                ->where('user_account_types.user_id', $user->id)
                ->whereIn('account_types.code', ['admin', 'platform_employee'])
                ->orderByDesc('user_account_types.id')
                ->select('user_account_types.id')
                ->first();

            if (! $accountType) {
                abort(403, 'No managed Admin/Employee account type found.');
            }

            DB::table('user_account_types')
                ->where('id', $accountType->id)
                ->update([
                    'status' => 'active',
                    'activated_at' => now(),
                    'updated_at' => now(),
                ]);

            // Sirf non-super-admin platform roles reactivate honge,
            // aur sirf jab unki start/expiry dates valid hon.
            DB::table('user_role_assignments')
                ->where('user_id', $user->id)
                ->where('scope_type', 'platform')
                ->whereNull('scope_id')
                ->where(function ($query) {
                    $query->whereNull('starts_at')
                        ->orWhere('starts_at', '<=', now());
                })
                ->where(function ($query) {
                    $query->whereNull('expires_at')
                        ->orWhere('expires_at', '>', now());
                })
                ->whereHas('role', function ($query) {
                    $query->where('code', '!=', 'super_admin');
                })
                ->update([
                    'is_active' => true,
                    'updated_at' => now(),
                ]);

            $this->writeAuditLog(
                $actor->id,
                'user.activated',
                $user->id,
                ['status' => 'inactive'],
                ['status' => 'active'],
                'Admin/Employee account activated from Super Admin panel.',
                'admin.users.activate'
            );
        });

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'User account activated successfully.');
    }

    /**
     * Ensure this is a managed Admin/Employee account.
     */
    private function ensureManagedAccount(User $user): void
    {
        if (! $this->isManagedAdminOrEmployee($user)) {
            abort(403, 'Only Admin/Employee accounts can be managed here.');
        }
    }

    private function isManagedAdminOrEmployee(User $user): bool
    {
        return $user->userAccountTypes()
            ->whereHas('accountType', function ($query) {
                $query->whereIn('code', ['admin', 'platform_employee']);
            })
            ->exists();
    }

    /**
     * Check all Super Admin assignments, including inactive ones.
     */
    private function isSuperAdminAccount(User $user): bool
    {
        return $user->roleAssignments()
            ->where('scope_type', 'platform')
            ->whereNull('scope_id')
            ->whereHas('role', function ($query) {
                $query->where('code', 'super_admin');
            })
            ->exists();
    }

    /**
     * Central audit log helper.
     */
    private function writeAuditLog(
        int $actorId,
        string $action,
        int $entityId,
        ?array $oldValues,
        ?array $newValues,
        string $reason,
        string $source
    ): void {
        DB::table('audit_logs')->insert([
            'actor_user_id' => $actorId,
            'action' => $action,
            'entity_type' => 'User',
            'entity_id' => $entityId,
            'reason' => $reason,
            'old_values' => $oldValues === null ? null : json_encode($oldValues),
            'new_values' => $newValues === null ? null : json_encode($newValues),
            'metadata' => json_encode(['source' => $source]),
            'ip_address' => request()->ip(),
            'request_id' => request()->header('X-Request-ID'),
            'created_at' => now(),
        ]);
    }
}
