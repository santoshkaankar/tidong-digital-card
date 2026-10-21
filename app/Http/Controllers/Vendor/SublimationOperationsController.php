<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/** Vendor-owned manual material stock records. No automatic POS/print consumption. */
class SublimationOperationsController extends Controller
{
    private const BUSINESS_TYPES = ['sublimation', 'custom_printing', 'printing'];
    private const UNITS = ['pcs', 'sheets', 'ml', 'meters', 'grams', 'packs'];

    private function context(Request $request, int $business): array
    {
        $owned = DB::table('businesses as b')
            ->join('vendors as v', 'v.id', '=', 'b.vendor_id')
            ->where('b.id', $business)->where('v.user_id', $request->user()->id)
            ->where('v.status', 'approved')->whereNull('v.deleted_at')->whereNull('b.deleted_at')
            ->whereIn('b.business_type', self::BUSINESS_TYPES)->select('b.*')->first();
        abort_unless($owned, 404);
        $branches = DB::table('branches')->where('business_id', $owned->id)
            ->whereNull('deleted_at')->orderBy('name')->get();
        $requested = $request->input('branch', $request->query('branch'));
        if ($requested === null || $requested === '') $branch = $branches->first();
        else {
            abort_unless(ctype_digit((string) $requested) && (int)$requested > 0, 404);
            $branch = $branches->firstWhere('id', (int)$requested);
            abort_unless($branch, 404);
        }
        return [
            'business' => $owned, 'branch' => $branch, 'branches' => $branches,
            'navBusiness' => $owned, 'navBranch' => $branch,
            'navBusinesses' => DB::table('businesses')
                ->where('vendor_id', $owned->vendor_id)->whereNull('deleted_at')->orderBy('name')->get(),
        ];
    }

    public function materials(Request $request, int $business): View
    {
        $ctx = $this->context($request, $business);
        $branch = $ctx['branch'];
        $materials = $branch ? DB::table('sublimation_materials')
            ->where('business_id', $business)->where('branch_id', $branch->id)
            ->orderBy('name')->paginate(30)->withQueryString() : null;
        $recent = $branch ? DB::table('sublimation_material_movements as m')
            ->join('sublimation_materials as i', 'i.id', '=', 'm.material_id')
            ->where('m.business_id', $business)->where('m.branch_id', $branch->id)
            ->select('m.*', 'i.name as material_name')->orderByDesc('m.id')->limit(20)->get() : collect();
        return view('vendor.sublimation.materials', array_merge($ctx, compact('materials','recent')));
    }

    public function createMaterial(Request $request, int $business): RedirectResponse
    {
        $ctx = $this->context($request, $business);
        $branch = $ctx['branch'];
        abort_unless($branch, 404);
        $data = $request->validate([
            'name' => ['required','string','max:160'],
            'sku' => ['nullable','string','max:80'],
            'unit' => ['required', Rule::in(self::UNITS)],
            'low_stock_level' => ['required','string','regex:/^[0-9]{1,7}(\\.[0-9]{1,3})?$/'],
        ]);
        $name = trim($data['name']);
        if ($name === '') throw ValidationException::withMessages(['name'=>'Enter a material name.']);
        DB::transaction(function () use ($request, $ctx, $branch, $business, $name, $data): void {
            $exists = DB::table('sublimation_materials')->where('business_id', $business)
                ->where('branch_id', $branch->id)->where('name', $name)->exists();
            if ($exists) throw ValidationException::withMessages(['name'=>'This material already exists in the branch.']);
            $id = DB::table('sublimation_materials')->insertGetId([
                'business_id'=>$business, 'branch_id'=>$branch->id, 'name'=>$name,
                'sku'=>trim((string)($data['sku']??'')) ?: null, 'unit'=>$data['unit'],
                'on_hand'=>'0.000','low_stock_level'=>$this->parseAmount($data['low_stock_level']),
                'created_at'=>now(),'updated_at'=>now(),
            ]);
            $this->audit($request, 'sublimation.material.created', 'sublimation_material', $id,
                ['business_id'=>$business,'branch_id'=>$branch->id,'name'=>$name]);
        });
        return redirect()->route('vendor.sublimation.materials.index', ['business'=>$business,'branch'=>$branch->id])
            ->with('success','Material added. Record a stock-in movement for opening balance.');
    }

    public function moveMaterial(Request $request, int $business, int $material): RedirectResponse
    {
        $ctx = $this->context($request, $business);
        $branch = $ctx['branch'];
        abort_unless($branch, 404);
        $data = $request->validate([
            'direction' => ['required', Rule::in(['in','out'])],
            'quantity' => ['required','string','regex:/^[0-9]{1,7}(\\.[0-9]{1,3})?$/'],
            'reason' => ['required','string','max:300'],
        ]);
        $amount = $this->toMilli($data['quantity']);
        if ($amount <= 0 || $amount > 1000000000) {
            throw ValidationException::withMessages(['quantity'=>'Enter a positive quantity up to 1,000,000.']);
        }
        DB::transaction(function () use ($request, $business, $material, $branch, $data, $amount): void {
            $current = DB::table('sublimation_materials')->where('id',$material)
                ->where('business_id',$business)->where('branch_id',$branch->id)->lockForUpdate()->firstOrFail();
            $old = $this->toMilli((string)$current->on_hand);
            $change = $data['direction'] === 'in' ? $amount : -$amount;
            $next = $old + $change;
            if ($next < 0) throw ValidationException::withMessages(['quantity'=>'Not enough stock in this branch.']);
            if ($next > 99999999999999) throw ValidationException::withMessages(['quantity'=>'Stock balance limit exceeded.']);
            DB::table('sublimation_materials')->where('id',$current->id)
                ->update(['on_hand'=>$this->formatMilli($next),'updated_at'=>now()]);
            $id = DB::table('sublimation_material_movements')->insertGetId([
                'business_id'=>$business,'branch_id'=>$branch->id,'material_id'=>$current->id,
                'actor_user_id'=>$request->user()->id, 'direction'=>$data['direction'],
                'quantity'=>$this->formatMilli($amount),'balance_after'=>$this->formatMilli($next),
                'reason'=>trim($data['reason']),'created_at'=>now(),
            ]);
            $this->audit($request,'sublimation.material.movement','sublimation_material_movement',$id,
                ['material_id'=>$material,'direction'=>$data['direction'],'quantity'=>$this->formatMilli($amount),'balance_after'=>$this->formatMilli($next)]);
        });
        return redirect()->route('vendor.sublimation.materials.index', ['business'=>$business,'branch'=>$branch->id])
            ->with('success','Stock updated; movement recorded.');
    }

    public function reports(Request $request, int $business): View
    {
        $ctx = $this->context($request, $business);
        $branch = $ctx['branch'];
        $products=0; $activeProducts=0; $orders=0; $quotedPaise=0; $estimatedCostPaise=0;
        $statuses=collect(); $lowMaterials=0; $recent=collect();
        if ($branch) {
            $productQuery=DB::table('sublimation_products')->where('business_id',$business)->where('branch_id',$branch->id);
            $orderQuery=DB::table('sublimation_orders')->where('business_id',$business)->where('branch_id',$branch->id);
            $products=(clone $productQuery)->count();
            $activeProducts=(clone $productQuery)->where('is_active',true)->count();
            $orders=(clone $orderQuery)->count();
            $quotedPaise=(int)(clone $orderQuery)->where('status','!=','cancelled')->sum('total_sale_paise');
            $estimatedCostPaise=(int)(clone $orderQuery)->where('status','!=','cancelled')->sum('total_cost_paise');
            $statuses=(clone $orderQuery)->select('status')->selectRaw('COUNT(*) as total')
                ->groupBy('status')->orderBy('status')->get();
            $lowMaterials=DB::table('sublimation_materials')->where('business_id',$business)
                ->where('branch_id',$branch->id)->whereColumn('on_hand','<=','low_stock_level')->count();
            $recent=(clone $orderQuery)->orderByDesc('id')->limit(10)->get();
        }
        return view('vendor.sublimation.reports',array_merge($ctx,compact(
            'products','activeProducts','orders','quotedPaise','estimatedCostPaise','statuses','lowMaterials','recent'
        )));
    }

    private function toMilli(string $value): int
    {
        [$whole, $decimal] = array_pad(explode('.',trim($value),2),2,'');
        return (int)$whole*1000 + (int)str_pad(substr($decimal,0,3),3,'0');
    }
    private function formatMilli(int $value): string
    {
        return intdiv($value,1000).'.'.str_pad((string)($value%1000),3,'0',STR_PAD_LEFT);
    }
    private function parseAmount(string $value): string {return $this->formatMilli($this->toMilli($value));}

    private function audit(Request $request,string $action,string $type,int $id,array $values): void
    {
        DB::table('audit_logs')->insert([
            'actor_user_id'=>$request->user()->id, 'action'=>$action, 'entity_type'=>$type,
            'entity_id'=>$id, 'new_values'=>json_encode($values),
            'ip_address'=>$request->ip(), 'created_at'=>now(),
        ]);
    }
}
