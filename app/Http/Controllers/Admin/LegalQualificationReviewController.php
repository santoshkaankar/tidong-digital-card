<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class LegalQualificationReviewController extends Controller
{
    public function index()
    {
        $applications=DB::table('legal_professional_qualifications as q')
            ->join('legal_professional_profiles as p','p.id','=','q.professional_id')
            ->where('q.verification_status','pending')
            ->select('q.*','p.display_name','p.user_id')->orderBy('q.id')->paginate(30);
        return view('admin.legal-features.qualifications', compact('applications'));
    }

    public function decide(Request $request,int $qualification)
    {
        $data=$request->validate([
            'decision'=>['required',Rule::in(['verified','rejected'])],
            'verification_reference'=>['required_if:decision,verified','nullable','string','min:8','max:250'],
            'reason'=>['required_if:decision,rejected','nullable','string','max:500'],
            'confirm_independent_check'=>['required_if:decision,verified','nullable','accepted'],
        ]);
        DB::transaction(function () use ($request,$qualification,$data) {
            $item=DB::table('legal_professional_qualifications')->where('id',$qualification)->lockForUpdate()->first();
            abort_unless($item,404);
            abort_unless($item->verification_status==='pending',422);
            $profile=DB::table('legal_professional_profiles')->where('id',$item->professional_id)->lockForUpdate()->first();
            abort_unless($profile,404);
            // No self-approval, including users who also have a platform role.
            abort_if((int)$profile->user_id === (int)$request->user()->id,403);
            $now=now();
            DB::table('legal_professional_qualifications')->where('id',$item->id)->update([
                'verification_status'=>$data['decision'],
                'verification_reference'=>$data['decision']==='verified' ? $data['verification_reference'] : null,
                'review_reason'=>$data['decision']==='rejected' ? $data['reason'] : null,
                'verified_by'=>$request->user()->id,'verified_at'=>$now,'updated_at'=>$now,
            ]);
            if ($data['decision']==='verified') DB::table('legal_professional_profiles')->where('id',$profile->id)->update([
                'verification_status'=>'verified','updated_at'=>$now]);
            DB::table('audit_logs')->insert(['actor_user_id'=>$request->user()->id,'action'=>'legal.qualification.reviewed',
                'entity_type'=>'legal_professional_qualification','entity_id'=>$item->id,
                'old_values'=>json_encode(['status'=>'pending']),
                'new_values'=>json_encode(['status'=>$data['decision']]),
                'metadata'=>json_encode(['verification_reference'=>$data['decision']==='verified' ? $data['verification_reference'] : null]),
                'ip_address'=>$request->ip(),'created_at'=>$now]);
        });
        return redirect()->route('admin.legal.qualifications.index')->with('success','Qualification review saved.');
    }
}
