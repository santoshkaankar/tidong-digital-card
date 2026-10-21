<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class EnsureDeliveryPartner
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user) return redirect()->guest(route('login'));
        $hasType = DB::table('user_account_types as uat')
            ->join('account_types as at','at.id','=','uat.account_type_id')
            ->where('uat.user_id',$user->id)->where('uat.status','active')
            ->where('at.code','delivery')->where('at.is_active',true)->exists();
        $hasRole = DB::table('user_role_assignments as ura')
            ->join('roles as r','r.id','=','ura.role_id')
            ->where('ura.user_id',$user->id)->where('ura.is_active',true)
            ->where('ura.scope_type','platform')->whereNull('ura.scope_id')
            ->where('r.code','delivery_partner')
            ->where(fn ($q)=>$q->whereNull('ura.starts_at')->orWhere('ura.starts_at','<=',now()))
            ->where(fn ($q)=>$q->whereNull('ura.expires_at')->orWhere('ura.expires_at','>',now()))->exists();
        $active = DB::table('delivery_partners')->where('user_id',$user->id)
            ->where('status','active')->exists();
        abort_unless($hasType && $hasRole && $active, 403, 'Delivery partner access denied.');
        return $next($request);
    }
}
