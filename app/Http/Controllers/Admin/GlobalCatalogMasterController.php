<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
class GlobalCatalogMasterController extends Controller {
    private const TYPES = ['brands'=>'Brands','item_categories'=>'Item Categories','units'=>'Units of Measure','variant_attributes'=>'Variant Attributes','variant_values'=>'Variant Values'];
    private function table(string $type): string {
        abort_unless(isset(self::TYPES[$type]),404);
        return 'global_catalog_'.$type;
    }
    public function index(string $type) {
        $table=$this->table($type);
        $records=DB::table($table)->orderBy('name')->paginate(30);
        $parents=in_array($type,['item_categories','variant_values'],true)
            ? DB::table($type==='variant_values'?'global_catalog_variant_attributes':$table)->orderBy('name')->get() : collect();
        return view('admin.global-catalog-master',compact('type','records','parents')+['title'=>self::TYPES[$type]]);
    }
    public function update(Request $request,string $type,int $record) {
        $table=$this->table($type);
        $data=$request->validate(['name'=>['required','string','max:150'],'code'=>['nullable','string','max:50'],'is_active'=>['required','boolean']]);
        $existing=DB::table($table)->where('id',$record)->first();
        abort_unless($existing,404);
        $duplicate=DB::table($table)->where('id','!=',$record)->where('parent_id',$existing->parent_id)
            ->whereRaw('LOWER(name) = ?', [mb_strtolower(trim($data['name']))])->exists();
        if ($duplicate) return back()->withErrors(['name'=>'Name already exists in this group.']);
        DB::table($table)->where('id',$record)->update(['name'=>trim($data['name']),'code'=>$data['code']??null,'is_active'=>(bool)$data['is_active'],'updated_at'=>now()]);
        return back()->with('success','Global record updated.');
    }
    public function store(Request $request,string $type) {
        $table=$this->table($type);
        $rules=['name'=>['required','string','min:1','max:150'],'code'=>['nullable','string','max:50'],
            'owner_name'=>['nullable','string','max:200'],'parent_id'=>['nullable','integer'],
            'is_active'=>['nullable','boolean']];
        if ($type==='variant_values') $rules['parent_id']=['required','integer',Rule::exists('global_catalog_variant_attributes','id')];
        if ($type==='item_categories') $rules['parent_id']=['nullable','integer',Rule::exists($table,'id')];
        if ($type==='variant_attributes') $rules['code']=['required','string','max:50'];
        $data=$request->validate($rules);
        $parent=in_array($type,['item_categories','variant_values'],true)?($data['parent_id']??null):null;
        $exists=DB::table($table)->whereRaw('LOWER(name) = ?', [mb_strtolower(trim($data['name']))])
            ->where('parent_id',$parent)->exists();
        if($exists) return back()->withErrors(['name'=>'This name already exists in the selected group.'])->withInput();
        if ($type==='item_categories' && $parent !== null) {
            $cursor=$parent;
            $seen=[];
            while ($cursor !== null) {
                if (isset($seen[$cursor])) return back()->withErrors(['parent_id'=>'Invalid category hierarchy.'])->withInput();
                $seen[$cursor]=true;
                $ancestor=DB::table($table)->where('id',$cursor)->first();
                if (!$ancestor) return back()->withErrors(['parent_id'=>'Parent category not found.'])->withInput();
                if (mb_strtolower(trim($ancestor->name))===mb_strtolower(trim($data['name']))) {
                    return back()->withErrors(['name'=>'A category cannot share a name with its ancestor.'])->withInput();
                }
                $cursor=$ancestor->parent_id;
            }
        }
        DB::table($table)->insert(['name'=>trim($data['name']),'code'=>$data['code']??null,
            'owner_name'=>$type==='brands'?($data['owner_name']??null):null,'parent_id'=>$parent,
            'is_active'=>$request->boolean('is_active',true),'created_by_user_id'=>$request->user()->id,
            'created_at'=>now(),'updated_at'=>now()]);
        return back()->with('success','Global master record created.');
    }
}
