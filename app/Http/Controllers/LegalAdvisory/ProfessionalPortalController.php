<?php
namespace App\Http\Controllers\LegalAdvisory;

use App\Http\Controllers\Controller;
use App\Support\LegalAdvisory\Feature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfessionalPortalController extends Controller
{
    private function profile(Request $request)
    {
        // Platform support must never impersonate or operate a professional engagement.
        abort_if($request->user()->userAccountTypes()->whereHas('accountType',fn($q)=>$q->whereIn('code',['platform_admin','admin','platform_employee']))->exists(),403);
        $profile=DB::table('legal_professional_profiles')->where('user_id',$request->user()->id)->first();
        abort_unless($profile && $profile->verification_status==='verified',403);
        return $profile;
    }
    public function index(Request $request)
    {
        abort_unless(Feature::enabled('professional_portal'),404);
        $profile=$this->profile($request);
        $engagements=DB::table('legal_engagements as e')->join('vendors as v','v.id','=','e.vendor_id')
            ->where('e.professional_id',$profile->id)->whereNull('v.deleted_at')
            ->select('e.*','v.display_name as vendor_name')->orderByDesc('e.id')->get();
        return view('legal-advisory.professional.index',compact('engagements'));
    }
    public function accept(Request $request,int $engagement)
    {
        abort_unless(Feature::enabled('professional_portal'),404);
        $profile=$this->profile($request);
        DB::transaction(function () use ($request,$profile,$engagement) {
            $item=DB::table('legal_engagements')->where('id',$engagement)->where('professional_id',$profile->id)->lockForUpdate()->first();
            abort_unless($item,404);
            abort_unless($item->status==='invited',422);
            abort_unless(DB::table('vendors')->where('id',$item->vendor_id)->where('status','approved')->whereNull('deleted_at')->exists(),403);
            DB::table('legal_engagements')->where('id',$engagement)->update(['status'=>'active','accepted_at'=>now(),'updated_at'=>now()]);
            DB::table('audit_logs')->insert(['actor_user_id'=>$request->user()->id,'action'=>'legal.engagement.accepted','entity_type'=>'legal_engagement','entity_id'=>$engagement,'metadata'=>json_encode(['vendor_id'=>$item->vendor_id]),'ip_address'=>$request->ip(),'created_at'=>now()]);
        });
        return redirect()->route('legal.professional.index')->with('success','Engagement accepted.');
    }
}
