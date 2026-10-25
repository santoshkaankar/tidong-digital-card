<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class VendorGlobalCatalogPermissionController extends Controller {
 public function index() {
  $vendors=DB::table('vendors')->leftJoin('vendor_global_catalog_permissions as grants','grants.vendor_id','=','vendors.id')
   ->select('vendors.id','vendors.name','vendors.status','grants.can_create','grants.can_edit')
   ->orderBy('vendors.id')->paginate(30);
  return view('admin.vendor-global-catalog-permissions',compact('vendors'));
 }
 public function update(Request $request,int $vendor) {
  $data=$request->validate(['can_create'=>['required','boolean'],'can_edit'=>['required','boolean']]);
  abort_unless(DB::table('vendors')->where('id',$vendor)->exists(),404);
  DB::table('vendor_global_catalog_permissions')->updateOrInsert(['vendor_id'=>$vendor],
   ['can_create'=>(bool)$data['can_create'],'can_edit'=>(bool)$data['can_edit'],
    'updated_by_user_id'=>$request->user()->id,'updated_at'=>now()]);
  return back()->with('success','Vendor Global Catalog permission updated.');
 }
}
