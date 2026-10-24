<?php
namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Services\Platform\ModuleRegistry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

final class PartnerApplicationController extends Controller
{
    private function type(Request $request): string
    {
        $user = $request->user();
        abort_unless($user, 403);
        // Reject platform administrators and employees in all circumstances.
        abort_if($user->userAccountTypes()->whereHas('accountType',
            fn ($q) => $q->whereIn('code',['platform_admin','admin','platform_employee']))->exists(),403);
        $types=DB::table('user_account_types as a')
            ->join('account_types as t','t.id','=','a.account_type_id')
            ->where('a.user_id',$user->id)->where('a.status','active')
            ->where('t.is_active',true)->whereIn('t.code',['professional','service_provider'])
            ->pluck('t.code')->all();
        // A dual role cannot submit under an implicitly chosen identity.
        abort_unless(count($types)===1,403,'Exactly one eligible partner account is required.');
        $validRole=DB::table('user_role_assignments as ura')
            ->join('roles as r','r.id','=','ura.role_id')
            ->where('ura.user_id',$user->id)->where('r.code',$types[0])
            ->where('ura.scope_type','platform')->whereNull('ura.scope_id')
            ->where('ura.is_active',true)
            ->where(fn($q)=>$q->whereNull('ura.starts_at')->orWhere('ura.starts_at','<=',now()))
            ->where(fn($q)=>$q->whereNull('ura.expires_at')->orWhere('ura.expires_at','>',now()))
            ->exists();
        abort_unless($validRole,403);
        return $types[0];
    }
    public function index(Request $request): View
    {
        $type=$this->type($request);
        $record=DB::table('partner_approval_applications')->where('user_id',$request->user()->id)
            ->where('account_type',$type)->first();
        $enabled=app(ModuleRegistry::class)->isEnabled('partner_approvals');
        return view('partner.application',compact('type','record','enabled'));
    }
    public function store(Request $request): RedirectResponse
    {
        $type=$this->type($request);
        abort_unless(app(ModuleRegistry::class)->isEnabled('partner_approvals'),403,'Partner applications are not enabled yet.');
        abort_unless($request->user()->email_verified_at,422,'Verify your email before submitting a profile.');
        $data=$request->validate([
            'service_title'=>['required','string','min:3','max:160'],
            'category'=>['required','string','min:2','max:120'],
            'registration_reference'=>['required','string','min:3','max:160'],
            'experience_description'=>['required','string','min:20','max:3000'],
        ]);
        DB::transaction(function()use($request,$type,$data):void{
            $old=DB::table('partner_approval_applications')->where('user_id',$request->user()->id)
                ->where('account_type',$type)->lockForUpdate()->first();
            if($old && $old->status!=='rejected'){
                throw ValidationException::withMessages(['application'=>'An application is already awaiting review or approved.']);
            }
            $now=now();$values=$data+['status'=>'pending','reviewed_by'=>null,'verification_reference'=>null,
                'review_reason'=>null,'reviewed_at'=>null,'updated_at'=>$now];
            if($old){
                DB::table('partner_approval_applications')->where('id',$old->id)->update($values);$id=$old->id;
            }else{
                $id=DB::table('partner_approval_applications')->insertGetId($values+[
                    'user_id'=>$request->user()->id,'account_type'=>$type,'created_at'=>$now]);
            }
            DB::table('audit_logs')->insert(['actor_user_id'=>$request->user()->id,
                'action'=>'partner.application.submitted','entity_type'=>'partner_approval_application',
                'entity_id'=>$id,'new_values'=>json_encode(['account_type'=>$type,'status'=>'pending']),
                'created_at'=>$now]);
        },3);
        return redirect()->route('registration.status')->with('success','Profile submitted for independent review.');
    }
}