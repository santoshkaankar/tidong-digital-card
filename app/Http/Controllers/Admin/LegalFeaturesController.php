<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\LegalAdvisory\Feature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class LegalFeaturesController extends Controller
{
    public function index()
    {
        $flags=DB::table('legal_feature_flags')->pluck('enabled','code');
        return view('admin.legal-features.index',compact('flags'));
    }
    public function update(Request $request)
    {
        $data=$request->validate(['code'=>['required',Rule::in(Feature::CODES)],'enabled'=>['required','boolean']]);
        DB::transaction(function () use ($request,$data) {
            $old=DB::table('legal_feature_flags')->where('code',$data['code'])->value('enabled');
            DB::table('legal_feature_flags')->updateOrInsert(['code'=>$data['code']],['enabled'=>$data['enabled'],'changed_by'=>$request->user()->id,'created_at'=>now(),'updated_at'=>now()]);
            DB::table('audit_logs')->insert(['actor_user_id'=>$request->user()->id,'action'=>'legal.feature.changed','entity_type'=>'legal_feature','entity_id'=>null,'old_values'=>json_encode(['code'=>$data['code'],'enabled'=>$old]),'new_values'=>json_encode($data),'ip_address'=>$request->ip(),'created_at'=>now()]);
        });
        return redirect()->route('admin.legal.features.index')->with('success','Feature setting saved.');
    }
}
