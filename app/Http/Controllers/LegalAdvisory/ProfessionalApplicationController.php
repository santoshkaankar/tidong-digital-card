<?php
namespace App\Http\Controllers\LegalAdvisory;

use App\Http\Controllers\Controller;
use App\Support\LegalAdvisory\Categories;
use App\Support\LegalAdvisory\Feature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProfessionalApplicationController extends Controller
{
    private function ensureEligible(Request $request): void
    {
        abort_unless(Feature::enabled('professional_portal'), 404);
        // Staff must never use the vendor/professional workspaces to manage client firms.
        abort_if($request->user()->userAccountTypes()->where('status', 'active')->whereHas('accountType',
            fn($q) => $q->whereIn('code', ['platform_admin','admin','platform_employee']))->exists(), 403);
    }

    public function index(Request $request)
    {
        $this->ensureEligible($request);
        $profile = DB::table('legal_professional_profiles')->where('user_id', $request->user()->id)->first();
        $qualifications = $profile ? DB::table('legal_professional_qualifications')->where('professional_id', $profile->id)->orderBy('category')->get() : collect();
        return view('legal-advisory.professional.application', compact('profile','qualifications'));
    }

    public function store(Request $request)
    {
        $this->ensureEligible($request);
        $data = $request->validate([
            'display_name' => ['required','string','min:2','max:200'],
            'category' => ['required',Rule::in(Categories::ALL)],
            'registration_reference' => ['required','string','max:120'],
            'applicant_note' => ['nullable','string','max:3000'],
        ]);
        DB::transaction(function () use ($request,$data) {
            $now=now();
            $profile=DB::table('legal_professional_profiles')->where('user_id',$request->user()->id)->lockForUpdate()->first();
            if (!$profile) {
                $id=DB::table('legal_professional_profiles')->insertGetId([
                    'user_id'=>$request->user()->id,'display_name'=>$data['display_name'],
                    'verification_status'=>'pending','created_at'=>$now,'updated_at'=>$now,
                ]);
            } else {
                $id=$profile->id;
                // A verified identity cannot be silently renamed by the applicant.
                if ($profile->verification_status === 'verified' && $profile->display_name !== $data['display_name'])
                    throw ValidationException::withMessages(['display_name'=>'Verified profile name cannot be changed here.']);
                if ($profile->verification_status !== 'verified')
                    DB::table('legal_professional_profiles')->where('id',$id)->update(['display_name'=>$data['display_name'],'updated_at'=>$now]);
            }
            $old=DB::table('legal_professional_qualifications')->where('professional_id',$id)->where('category',$data['category'])->lockForUpdate()->first();
            if ($old && in_array($old->verification_status,['verified','pending'],true))
                throw ValidationException::withMessages(['category'=>'This qualification is already verified or awaiting review.']);
            $values=['registration_reference'=>$data['registration_reference'],'applicant_note'=>$data['applicant_note']??null,
                'verification_status'=>'pending','verification_reference'=>null,'verified_by'=>null,
                'verified_at'=>null,'review_reason'=>null,'updated_at'=>$now];
            if ($old) {
                DB::table('legal_professional_qualifications')->where('id',$old->id)->update($values);
                $qid=$old->id;
            } else {
                $qid=DB::table('legal_professional_qualifications')->insertGetId($values+[
                    'professional_id'=>$id,'category'=>$data['category'],'created_at'=>$now]);
            }
            DB::table('audit_logs')->insert(['actor_user_id'=>$request->user()->id,'action'=>'legal.qualification.submitted',
                'entity_type'=>'legal_professional_qualification','entity_id'=>$qid,
                'metadata'=>json_encode(['category'=>$data['category']]),'ip_address'=>$request->ip(),'created_at'=>$now]);
        });
        return redirect()->route('legal.application.index')->with('success','Application submitted for independent verification. No client access has been granted.');
    }
}
