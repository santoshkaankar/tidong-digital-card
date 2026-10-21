<?php
namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Support\LegalAdvisory\Feature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class LegalAdvisoryController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(Feature::enabled('module'), 404);
        $vendor = $request->user()->vendor()->firstOrFail();
        $engagements = DB::table('legal_engagements as e')
            ->join('legal_professional_profiles as p','p.id','=','e.professional_id')
            ->where('e.vendor_id', $vendor->id)
            ->select('e.*','p.display_name as professional_name')->orderByDesc('e.id')->get();
        return view('vendor.legal-advisory.index', compact('engagements'));
    }

    public function invite(Request $request)
    {
        abort_unless(Feature::enabled('hiring'), 404);
        $data = $request->validate([
            'professional_user_id' => ['required','integer','exists:users,id'],
            'category' => ['required',Rule::in(['ca','cs','advocate','tax_advocate','gst_consultant','tax_consultant','cma','ip_professional','other'])],
            'scope' => ['required',Rule::in(['vendor','business','branch'])],
            'business_id' => ['nullable','integer'], 'branch_id' => ['nullable','integer'],
            'permissions' => ['required','array','min:1'],
            'permissions.*' => [Rule::in(['case_view','case_manage','document_view','document_upload','compliance_view'])],
        ]);
        $vendor = $request->user()->vendor()->firstOrFail();
        abort_unless($vendor->status === 'approved', 403);
        abort_if((int)$data['professional_user_id'] === (int)$request->user()->id, 422);
        $scope = $data['scope'];
        $business = $data['business_id'] ?? null;
        $branch = $data['branch_id'] ?? null;
        if ($scope === 'vendor' && ($business || $branch) || $scope === 'business' && (!$business || $branch) || $scope === 'branch' && (!$business || !$branch)) {
            throw ValidationException::withMessages(['scope' => 'Scope and business/branch selection do not match.']);
        }
        if ($business && !DB::table('businesses')->where('id',$business)->where('vendor_id',$vendor->id)->whereNull('deleted_at')->exists()) abort(404);
        if ($branch && !DB::table('branches')->where('id',$branch)->where('business_id',$business)->exists()) abort(404);
        DB::transaction(function () use ($data,$vendor,$request,$business,$branch,$scope) {
            $profile = DB::table('legal_professional_profiles')->where('user_id',$data['professional_user_id'])->lockForUpdate()->first();
            if (!$profile) throw ValidationException::withMessages(['professional_user_id' => 'A verified Professional profile is required before invitation.']);
            if ($profile->verification_status !== 'verified' || !DB::table('legal_professional_qualifications')->where('professional_id',$profile->id)->where('category',$data['category'])->where('verification_status','verified')->exists()) {
                throw ValidationException::withMessages(['category' => 'Qualification verification required.']);
            }
            $now=now();
            $id=DB::table('legal_engagements')->insertGetId([
                'vendor_id'=>$vendor->id,'professional_id'=>$profile->id,'business_id'=>$business,'branch_id'=>$branch,
                'scope'=>$scope,'category'=>$data['category'],'status'=>'invited',
                'permissions'=>json_encode(array_values(array_unique($data['permissions']))),
                'invited_by'=>$request->user()->id,'created_at'=>$now,'updated_at'=>$now,
            ]);
            $this->audit($request,'legal.engagement.invited',$id,['vendor_id'=>$vendor->id,'professional_id'=>$profile->id,'scope'=>$scope,'business_id'=>$business,'branch_id'=>$branch]);
        });
        return redirect()->route('vendor.legal.index')->with('success','Invitation created. The Professional must accept it.');
    }

    public function end(Request $request,int $engagement)
    {
        abort_unless(Feature::enabled('module'),404);
        $vendor=$request->user()->vendor()->firstOrFail();
        DB::transaction(function () use ($request,$vendor,$engagement) {
            $record=DB::table('legal_engagements')->where('id',$engagement)->where('vendor_id',$vendor->id)->lockForUpdate()->first();
            abort_unless($record,404);
            abort_if($record->status === 'ended',422);
            DB::table('legal_engagements')->where('id',$engagement)->update(['status'=>'ended','ended_at'=>now(),'updated_at'=>now()]);
            $this->audit($request,'legal.engagement.ended',$engagement,['vendor_id'=>$vendor->id]);
        });
        return redirect()->route('vendor.legal.index')->with('success','Engagement ended.');
    }
    private function audit(Request $request,string $action,int $id,array $metadata): void
    {
        DB::table('audit_logs')->insert(['actor_user_id'=>$request->user()->id,'action'=>$action,'entity_type'=>'legal_engagement','entity_id'=>$id,'metadata'=>json_encode($metadata),'ip_address'=>$request->ip(),'created_at'=>now()]);
    }
}
