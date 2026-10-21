<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Platform\PlatformCapability;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EmployeeCapabilityController extends Controller
{
    public function index(): View
    {
        $employees = DB::table('users as u')
            ->join('user_account_types as uat', 'uat.user_id','=','u.id')
            ->join('account_types as at', 'at.id','=','uat.account_type_id')
            ->whereIn('at.code',['admin','platform_employee'])->where('uat.status','active')
            ->whereExists(function ($q) {
                $q->selectRaw('1')->from('user_role_assignments as ura')
                    ->join('roles as r','r.id','=','ura.role_id')
                    ->whereColumn('ura.user_id', 'u.id')->where('ura.is_active',true)
                    ->where('ura.scope_type','platform')->whereNull('ura.scope_id')
                    ->whereNotIn('r.code',['admin','super_admin','delivery_partner']);
            })->distinct()->orderBy('u.name')->get(['u.id','u.name','u.email']);
        $grants = DB::table('platform_employee_permissions')->get()->groupBy('user_id');
        return view('admin.modules.permissions', compact('employees','grants'));
    }

    public function update(Request $request, PlatformCapability $capabilities): RedirectResponse
    {
        $data = $request->validate([
            'user_id' => ['required','integer','exists:users,id'],
            'permission_code' => ['required', Rule::in(PlatformCapability::CAPABILITIES)],
            'enabled' => ['required','boolean'],
        ]);
        $employee = \App\Models\User::findOrFail($data['user_id']);
        abort_unless($capabilities->isEmployee($employee) && ! $capabilities->isSupervisor($employee), 403);
        DB::transaction(function () use ($request,$data): void {
            $key = ['user_id' => $data['user_id'], 'permission_code' => $data['permission_code']];
            $existing = DB::table('platform_employee_permissions')->where($key)->lockForUpdate()->first();
            DB::table('platform_employee_permissions')->updateOrInsert($key, [
                'is_enabled' => (bool) $data['enabled'], 'updated_by' => $request->user()->id,
                'updated_at' => now(), 'created_at' => $existing->created_at ?? now(),
            ]);
            DB::table('audit_logs')->insert([
                'actor_user_id'=>$request->user()->id,'action'=>'employee.capability.changed',
                'entity_type'=>'user','entity_id'=>$data['user_id'],
                'old_values'=>json_encode(['enabled'=>$existing ? (bool) $existing->is_enabled : null]),
                'new_values'=>json_encode(['permission_code'=>$data['permission_code'],'enabled'=>(bool)$data['enabled']]),
                'created_at'=>now(),
            ]);
        });
        return back()->with('success','Employee permission updated and audited.');
    }
}
