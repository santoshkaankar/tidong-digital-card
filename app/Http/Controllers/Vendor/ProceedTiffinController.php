<?php
namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Services\Food\ProceedTiffinPricing as Price;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

final class ProceedTiffinController extends Controller
{
    private function today(): string {
        return Carbon::now(config('proceed_tiffin.timezone','Asia/Kolkata'))->toDateString();
    }

    /** Never accept a branch without matching authenticated vendor, business and Tiffin type. */
    private function outlet(Request $request,int $business,int $branch,bool $write=false): object {
        $outlet=DB::table('branches as br')
          ->join('businesses as bu','bu.id','=','br.business_id')
          ->join('vendors as ve','ve.id','=','bu.vendor_id')
          ->where('br.id',$branch)->where('bu.id',$business)
          ->where('ve.user_id',$request->user()->id)
          ->where('bu.business_type','tiffin')
          ->whereNull('br.deleted_at')->whereNull('bu.deleted_at')->whereNull('ve.deleted_at')
          ->select('br.id','br.name','br.status as branch_status','bu.id as business_id',
              'bu.name as business_name','bu.status as business_status','ve.id as vendor_id','ve.status as vendor_status')
          ->firstOrFail();
        if($write && ($outlet->branch_status!=='active' || $outlet->business_status!=='approved'
          || $outlet->vendor_status!=='approved')){
            throw ValidationException::withMessages(['outlet'=>'This Tiffin outlet needs an approved vendor/business and active branch.']);
        }
        return $outlet;
    }
    private function audit(Request $request,string $action,string $entity,int $id,array $values): void {
        DB::table('audit_logs')->insert([
            'actor_user_id'=>$request->user()->id,'action'=>$action,'entity_type'=>$entity,'entity_id'=>$id,
            'new_values'=>json_encode($values),'created_at'=>now(),
        ]);
    }

    public function index(Request $request,int $business,int $branch): View {
        $outlet=$this->outlet($request,$business,$branch);
        $items=DB::table('proceed_tiffin_items')->where('branch_id',$branch)->orderBy('name')->get();
        $catalog=DB::table('global_menu_items')->where('is_active',true)
            ->whereNotNull('mrp_paise')->where('mrp_paise','>',0)->orderBy('category')
            ->orderBy('name')->limit(200)->get();
        $dayMeals=DB::table('proceed_tiffin_meals')->where('branch_id',$branch)
            ->where('schedule_scope','day')->where('service_date','>=',$this->today())
            ->orderBy('service_date')->limit(80)->get();
        $everydayMeals=DB::table('proceed_tiffin_meals')->where('branch_id',$branch)
            ->where('schedule_scope','everyday')->orderBy('meal_type')->get();
        $meals=$dayMeals->concat($everydayMeals);
        $todayOffers=$meals->filter(fn($m)=>$m->status==='published' &&
            ($m->schedule_scope==='everyday' || $m->service_date===$this->today()));
        $orders=DB::table('proceed_tiffin_orders')->where('branch_id',$branch)
            ->orderByDesc('id')->limit(50)->get();
        $navBusiness=(object)['id'=>$outlet->business_id,'name'=>$outlet->business_name,
            'business_type'=>'tiffin','status'=>$outlet->business_status];
        $navBranch=(object)['id'=>$outlet->id,'name'=>$outlet->name];
        $navBusinesses=DB::table('businesses')->where('vendor_id',$outlet->vendor_id)
            ->whereNull('deleted_at')->orderBy('name')->get();
        return view('vendor.proceed-tiffin.index',compact(
            'outlet','items','catalog','meals','todayOffers','orders','navBusiness','navBranch','navBusinesses'
        ))->with('mealTypes',config('proceed_tiffin.slots',[]))->with('today',$this->today());
    }

    public function attachGlobal(Request $request,int $business,int $branch): RedirectResponse {
        $data=$request->validate([
            'global_menu_item_id'=>['required','integer','min:1'],
            'mrp'=>['nullable','string','regex:/^\d{1,7}(\.\d{1,2})?$/'],
            'sale_price'=>['nullable','string','regex:/^\d{1,7}(\.\d{1,2})?$/'],
        ]);
        DB::transaction(function()use($request,$business,$branch,$data):void{
            $this->outlet($request,$business,$branch,true);
            $global=DB::table('global_menu_items')->where('id',$data['global_menu_item_id'])
                ->where('is_active',true)->first();
            if(!$global || !$global->mrp_paise)throw ValidationException::withMessages([
                'global_menu_item_id'=>'Choose a global single item with an MRP.'
            ]);
            if(DB::table('proceed_tiffin_items')->where('branch_id',$branch)
                ->where('global_menu_item_id',$global->id)->exists()){
                throw ValidationException::withMessages(['global_menu_item_id'=>'This item is already selected in this outlet.']);
            }
            $mrp=($data['mrp']??'')!=='' ? Price::rupeesToPaise($data['mrp']) : (int)$global->mrp_paise;
            $defaultSale=(int)($global->sale_price_paise ?: $global->mrp_paise);
            $sale=($data['sale_price']??'')!=='' ? Price::rupeesToPaise($data['sale_price']) : min($defaultSale,$mrp);
            if($mrp<1||$sale<1||$sale>$mrp)throw ValidationException::withMessages(['sale_price'=>'Sale price must be positive and not exceed MRP.']);
            $id=DB::table('proceed_tiffin_items')->insertGetId([
                'branch_id'=>$branch,'global_menu_item_id'=>$global->id,'name'=>$global->name,
                'mrp_paise'=>$mrp,'sale_price_paise'=>$sale,'is_active'=>true,
                'created_at'=>now(),'updated_at'=>now(),
            ]);
            $this->audit($request,'proceed_tiffin.global_selected','proceed_tiffin_item',$id,
                ['branch_id'=>$branch,'global_menu_item_id'=>$global->id,'mrp_paise'=>$mrp,'sale_price_paise'=>$sale]);
        });
        return back()->with('success','Global single item added to this outlet only.');
    }

    public function createCustom(Request $request,int $business,int $branch): RedirectResponse {
        $data=$request->validate([
            'name'=>['required','string','min:2','max:150'],
            'mrp'=>['required','string','regex:/^\d{1,7}(\.\d{1,2})?$/'],
            'sale_price'=>['nullable','string','regex:/^\d{1,7}(\.\d{1,2})?$/'],
        ]);
        if(preg_match('/\b(thali|combo|platter|meal\s*deal)\b/i',$data['name'])){
            throw ValidationException::withMessages(['name'=>'Create single items only. Use Everyday Thali for the meal itself.']);
        }
        $mrp=Price::rupeesToPaise($data['mrp']);$sale=Price::sale((string)($data['sale_price']??''),$mrp);
        DB::transaction(function()use($request,$business,$branch,$data,$mrp,$sale):void{
            $this->outlet($request,$business,$branch,true);
            if(DB::table('proceed_tiffin_items')->where('branch_id',$branch)
                ->where('name',trim($data['name']))->exists()){
                throw ValidationException::withMessages(['name'=>'An item with this name already exists in your outlet.']);
            }
            $id=DB::table('proceed_tiffin_items')->insertGetId([
                'branch_id'=>$branch,'name'=>trim($data['name']),'mrp_paise'=>$mrp,
                'sale_price_paise'=>$sale,'is_active'=>true,'created_at'=>now(),'updated_at'=>now()
            ]);
            $this->audit($request,'proceed_tiffin.custom_item_created','proceed_tiffin_item',$id,['branch_id'=>$branch]);
        });
        return back()->with('success','Custom single item saved for this outlet only.');
    }

    public function saveMeal(Request $request,int $business,int $branch): RedirectResponse {
        $data=$request->validate([
            'schedule_scope'=>['required',Rule::in(['day','everyday'])],
            'service_date'=>['nullable','date_format:Y-m-d'],
            'meal_type'=>['required',Rule::in(array_keys(config('proceed_tiffin.slots',[])))],
            'name'=>['required','string','min:2','max:150'],
            'sale_price'=>['nullable','string','regex:/^\d{1,7}(\.\d{1,2})?$/'],
            'components'=>['required','array','min:1','max:200'],
            'components.*.item_id'=>['required','integer','min:1','distinct'],
            'components.*.quantity'=>['required','integer','min:0','max:100'],
        ]);
        $scope=$data['schedule_scope'];$date=null;
        if($scope==='day'){
            if(empty($data['service_date']))throw ValidationException::withMessages(['service_date'=>'Select a date for Day Tiffin.']);
            $date=Carbon::createFromFormat('!Y-m-d',$data['service_date'],config('proceed_tiffin.timezone','Asia/Kolkata'));
            $today=Carbon::createFromFormat('!Y-m-d',$this->today(),config('proceed_tiffin.timezone','Asia/Kolkata'));
            if(!$date || $date->format('Y-m-d')!==$data['service_date'] || $date->lt($today) || $date->gt($today->copy()->addDays(30))){
                throw ValidationException::withMessages(['service_date'=>'Select today or a day within 30 days.']);
            }
            $date=$data['service_date'];
        }
        $selected=collect($data['components'])->filter(fn($row)=>(int)$row['quantity']>0)->values();
        if($selected->isEmpty()||$selected->count()>25)
            throw ValidationException::withMessages(['components'=>'Select 1 to 25 individual items.']);
        $schedule=Price::scheduleKey($scope,$date??'',$data['meal_type']);
        DB::transaction(function()use($request,$business,$branch,$data,$scope,$date,$schedule,$selected):void{
            $this->outlet($request,$business,$branch,true);
            $ids=$selected->pluck('item_id')->map(fn($id)=>(int)$id)->all();
            $items=DB::table('proceed_tiffin_items')->where('branch_id',$branch)
                ->where('is_active',true)->whereIn('id',$ids)->lockForUpdate()->get()->keyBy('id');
            if($items->count()!==count($ids))throw ValidationException::withMessages([
                'components'=>'Selected item is disabled or belongs to another outlet.'
            ]);
            $rows=[];
            foreach($selected as $choice){
                $item=$items->get((int)$choice['item_id']);$qty=(int)$choice['quantity'];
                $rows[]=['item_id'=>$item->id,'name_snapshot'=>$item->name,
                    'quantity'=>$qty,'unit_mrp_paise'=>(int)$item->mrp_paise,
                    'line_total_paise'=>(int)$item->mrp_paise*$qty,
                    'created_at'=>now(),'updated_at'=>now()];
            }
            $mrp=Price::total($rows);
            $sale=Price::sale((string)($data['sale_price']??''),$mrp);
            $record=DB::table('proceed_tiffin_meals')->where('branch_id',$branch)
                ->where('schedule_key',$schedule)->lockForUpdate()->first();
            if($record&&$record->status!=='draft')throw ValidationException::withMessages([
                'meal_type'=>'A published Tiffin/Thali cannot be edited. Create an approved revision workflow later.'
            ]);
            $fields=['name'=>trim($data['name']),'mrp_total_paise'=>$mrp,
                'sale_price_paise'=>$sale,'updated_at'=>now()];
            if($record){
                $mealId=(int)$record->id;
                DB::table('proceed_tiffin_meals')->where('id',$mealId)->update($fields);
                DB::table('proceed_tiffin_meal_items')->where('meal_id',$mealId)->delete();
            }else{
                $mealId=DB::table('proceed_tiffin_meals')->insertGetId($fields+[
                    'branch_id'=>$branch,'schedule_key'=>$schedule,
                    'schedule_scope'=>$scope,'service_date'=>$date,'meal_type'=>$data['meal_type'],
                    'status'=>'draft','created_at'=>now(),
                ]);
            }
            foreach($rows as &$row){$row['meal_id']=$mealId;}unset($row);
            DB::table('proceed_tiffin_meal_items')->insert($rows);
            $this->audit($request,'proceed_tiffin.meal_saved','proceed_tiffin_meal',$mealId,
                ['schedule_key'=>$schedule,'mrp_paise'=>$mrp,'sale_paise'=>$sale]);
        },3);
        return back()->with('success',$scope==='day'?'Day Tiffin draft saved.':'Everyday Thali draft saved.');
    }

    public function publish(Request $request,int $business,int $branch,int $meal): RedirectResponse {
        DB::transaction(function()use($request,$business,$branch,$meal):void{
            $this->outlet($request,$business,$branch,true);
            $row=DB::table('proceed_tiffin_meals')->where('id',$meal)
                ->where('branch_id',$branch)->lockForUpdate()->firstOrFail();
            if($row->status!=='draft'||($row->schedule_scope==='day'&&$row->service_date<$this->today())){
                throw ValidationException::withMessages(['meal'=>'Only current/future draft meals can be published.']);
            }
            DB::table('proceed_tiffin_meals')->where('id',$meal)->update([
                'status'=>'published','published_at'=>now(),'updated_at'=>now()]);
            $this->audit($request,'proceed_tiffin.meal_published','proceed_tiffin_meal',$meal,['scope'=>$row->schedule_scope]);
        });
        return back()->with('success','Meal published. Day Tiffin appears only on its day; Everyday Thali repeats.');
    }

    public function placeOrder(Request $request,int $business,int $branch,int $meal): RedirectResponse {
        $data=$request->validate(['quantity'=>['required','integer','min:1','max:50'],
            'customer_name'=>['nullable','string','max:100']]);
        DB::transaction(function()use($request,$business,$branch,$meal,$data):void{
            $this->outlet($request,$business,$branch,true);
            $row=DB::table('proceed_tiffin_meals')->where('id',$meal)
                ->where('branch_id',$branch)->lockForUpdate()->firstOrFail();
            if($row->status!=='published'||($row->schedule_scope==='day'&&$row->service_date!==$this->today())){
                throw ValidationException::withMessages(['meal'=>'Only todays published Tiffin or active Everyday Thali may be ordered.']);
            }
            $qty=(int)$data['quantity'];
            $id=DB::table('proceed_tiffin_orders')->insertGetId([
                'order_number'=>'PTF-'.Str::upper((string)Str::ulid()),
                'branch_id'=>$branch,'meal_id'=>$meal,'created_by_user_id'=>$request->user()->id,
                'meal_name_snapshot'=>$row->name,'schedule_scope'=>$row->schedule_scope,
                'service_date'=>$this->today(),'quantity'=>$qty,
                'unit_price_paise'=>$row->sale_price_paise,'total_paise'=>$row->sale_price_paise*$qty,
                'customer_name'=>trim((string)($data['customer_name']??''))?:null,
                'status'=>'received','payment_status'=>'unpaid',
                'placed_at'=>now(),'created_at'=>now(),'updated_at'=>now(),
            ]);
            $this->audit($request,'proceed_tiffin.order_received','proceed_tiffin_order',$id,['branch_id'=>$branch,'meal_id'=>$meal,'quantity'=>$qty]);
        },3);
        return back()->with('success','Counter order recorded. Payment remains UNPAID.');
    }
}
