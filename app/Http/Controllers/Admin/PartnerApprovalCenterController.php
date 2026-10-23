<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Platform\ModuleRegistry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

final class PartnerApprovalCenterController extends Controller
{
    public function index(): View
    {
        $counts=[
            'email'=>DB::table('users as u')->join('user_account_types as uat','uat.user_id','=','u.id')
                ->join('account_types as t','t.id','=','uat.account_type_id')
                ->where('uat.status','active')->whereIn('t.code',['vendor','delivery','professional','service_provider'])
                ->whereNull('u.email_verified_at')->distinct()->count('u.id'),
            'vendor'=>DB::table('vendors')->where('status','pending')->whereNull('deleted_at')->count(),
            'business'=>DB::table('businesses')->where('status','pending')->whereNull('deleted_at')->count(),
            'branch'=>DB::table('branch_change_requests')->where('status','pending')->count(),
            'delivery'=>DB::table('delivery_partners')->where('status','pending')->whereNull('approved_at')->count(),
            'professional'=>DB::table('partner_approval_applications')->where('account_type','professional')->where('status','pending')->count(),
            'service_provider'=>DB::table('partner_approval_applications')->where('account_type','service_provider')->where('status','pending')->count(),
            'qualification'=>DB::table('legal_professional_qualifications')->where('verification_status','pending')->count(),
        ];
        $blocked=DB::table('users as u')->join('user_account_types as a','a.user_id','=','u.id')
            ->join('account_types as t','t.id','=','a.account_type_id')
            ->where('a.status','active')->whereNull('u.email_verified_at')
            ->whereIn('t.code',['vendor','delivery','professional','service_provider'])
            ->select('u.id','u.name','u.email','t.code')->orderBy('u.id')->limit(30)->get();
        $enabled=app(ModuleRegistry::class)->isEnabled('partner_approvals');
        return view('admin.approval-center.index',compact('counts','blocked','enabled'));
    }
    public function queue(string $type): View
    {
        abort_unless(in_array($type,['professional','service_provider'],true),404);
        $records=DB::table('partner_approval_applications as a')->join('users as u','u.id','=','a.user_id')
            ->where('a.account_type',$type)->select('a.*','u.name','u.email','u.email_verified_at')
            ->orderByRaw("CASE WHEN a.status='pending' THEN 0 ELSE 1 END")
            ->orderByDesc('a.id')->paginate(20);
        $enabled=app(ModuleRegistry::class)->isEnabled('partner_approvals');
        return view('admin.approval-center.queue',compact('records','type','enabled'));
    }
    public function decide(Request $request,string $type,int $user): RedirectResponse
    {
        abort_unless(in_array($type,['professional','service_provider'],true),404);
        abort_unless(app(ModuleRegistry::class)->isEnabled('partner_approvals'),403,
            'Approval module is OFF. Enable in Module Management after review.');
        $data=$request->validate([
            'decision'=>['required',Rule::in(['approved','rejected'])],
            'verification_reference'=>['required_if:decision,approved','nullable','string','min:8','max:250'],
            'confirm_independent_check'=>['required_if:decision,approved','nullable','accepted'],
            'reason'=>['required_if:decision,rejected','nullable','string','min:5','max:500'],
        ]);
        abort_if((int)$request->user()->id===$user,403,'Self approval is forbidden.');
        DB::transaction(function()use($request,$type,$user,$data):void{
            $item=DB::table('partner_approval_applications')->where('user_id',$user)
                ->where('account_type',$type)->lockForUpdate()->firstOrFail();
            if($item->status!=='pending')throw ValidationException::withMessages(['decision'=>'Application has already been reviewed.']);
            $account=DB::table('user_account_types as a')->join('account_types as t','t.id','=','a.account_type_id')
                ->where('a.user_id',$user)->where('a.status','active')->where('t.is_active',true)
                ->where('t.code',$type)->exists();
            $role=DB::table('user_role_assignments as a')->join('roles as r','r.id','=','a.role_id')
                ->where('a.user_id',$user)->where('a.scope_type','platform')->whereNull('a.scope_id')
                ->where('a.is_active',true)->where('r.code',$type)
                ->where(fn($q)=>$q->whereNull('a.starts_at')->orWhere('a.starts_at','<=',now()))
                ->where(fn($q)=>$q->whereNull('a.expires_at')->orWhere('a.expires_at','>',now()))
                ->exists();
            if(!$account||!$role)throw ValidationException::withMessages(['decision'=>'Valid account and role are required.']);
            if($data['decision']==='approved' && !DB::table('users')->where('id',$user)
                ->whereNotNull('email_verified_at')->exists()){
                throw ValidationException::withMessages(['decision'=>'Email must be verified first.']);
            }
            $now=now();
            DB::table('partner_approval_applications')->where('id',$item->id)->update([
                'status'=>$data['decision'],'reviewed_by'=>$request->user()->id,
                'verification_reference'=>$data['decision']==='approved'?$data['verification_reference']:null,
                'review_reason'=>$data['decision']==='rejected'?$data['reason']:null,
                'reviewed_at'=>$now,'updated_at'=>$now,
            ]);
            DB::table('audit_logs')->insert([
                'actor_user_id'=>$request->user()->id,'action'=>'partner.application.'.$data['decision'],
                'entity_type'=>'partner_approval_application','entity_id'=>$item->id,
                'old_values'=>json_encode(['status'=>'pending']),
                'new_values'=>json_encode(['status'=>$data['decision'],'account_type'=>$type]),
                'metadata'=>json_encode(['evidence_reference'=>$data['decision']==='approved'?$data['verification_reference']:null]),
                'created_at'=>$now,
            ]);
        },3);
        // Approval is identity only. No vendor access or unrestricted dashboard.
        return redirect()->route('admin.approval-center.queue',$type)->with('success','Review recorded. Other access checks remain in force.');
    }
}