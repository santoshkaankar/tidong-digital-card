<?php
namespace App\Http\Controllers\Vendor;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\GlobalCatalogMasterController;
use App\Models\Vendor\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class VendorGlobalMasterController extends Controller {
 private function allowed(Request $request): void {
  $vendor=Vendor::query()->where('user_id',$request->user()->id)->where('status','approved')->firstOrFail();
  abort_unless(DB::table('vendor_global_catalog_permissions')->where('vendor_id',$vendor->id)->where('can_create',true)->exists(),403,'Global Master Create permission is disabled.');
 }
 public function index(Request $request,string $type) {
  $this->allowed($request);
  abort_unless(in_array($type,['brands','item_categories','units','variant_attributes','variant_values'],true),404);
  $records=DB::table('global_catalog_'.$type)->orderBy('name')->paginate(30);
  $parents=in_array($type,['item_categories','variant_values'],true)
   ? DB::table($type==='variant_values'?'global_catalog_variant_attributes':'global_catalog_item_categories')->orderBy('name')->get():collect();
  return view('admin.global-catalog-master',compact('type','records','parents')+['title'=>'Global '.ucwords(str_replace('_',' ',$type)),'vendorGlobalMode'=>true]);
 }
 public function store(Request $request,string $type,GlobalCatalogMasterController $controller) {
  $this->allowed($request);
  return $controller->store($request,$type);
 }
}
