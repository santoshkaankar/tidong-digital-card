<?php
namespace App\Http\Controllers\Vendor;
use App\Http\Controllers\Controller;
use App\Models\Vendor\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
class CustomCatalogItemController extends Controller {
 private function branch(Request $request,int $branch) {
  $vendor=Vendor::where('user_id',$request->user()->id)->where('status','approved')->firstOrFail();
  $owned=DB::table('branches')->join('businesses','businesses.id','=','branches.business_id')
    ->where('branches.id',$branch)->whereNull('branches.deleted_at')->whereNull('businesses.deleted_at')->where('businesses.vendor_id',$vendor->id)
    ->select('branches.id')->first();
  abort_unless($owned,404);
  return $vendor;
 }
 public function index(Request $request,int $branch) {
  $this->branch($request,$branch);
  $items=DB::table('vendor_custom_catalog_items')->where('branch_id',$branch)->orderBy('name')->paginate(25);
  return view('vendor.custom-catalog-items',compact('items','branch'));
 }
 public function store(Request $request,int $branch) {
  $vendor=$this->branch($request,$branch);
  $data=$request->validate(['name'=>['required','string','max:150'],'description'=>['nullable','string','max:1500'],
    'mrp'=>['required','numeric','min:0','max:9999999999'],'sale_price'=>['required','numeric','min:0','lte:mrp']]);
  if(DB::table('vendor_custom_catalog_items')->where('branch_id',$branch)->where('name',trim($data['name']))->exists())
    throw ValidationException::withMessages(['name'=>'This outlet already has a custom item with this name.']);
  DB::table('vendor_custom_catalog_items')->insert(['vendor_id'=>$vendor->id,'branch_id'=>$branch,
    'name'=>trim($data['name']),'description'=>$data['description']??null,'mrp'=>$data['mrp'],
    'sale_price'=>$data['sale_price'],'created_at'=>now(),'updated_at'=>now()]);
  return back()->with('success','Custom item saved for this outlet only.');
 }
}
