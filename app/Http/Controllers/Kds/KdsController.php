<?php

namespace App\Http\Controllers\Kds;

use App\Http\Controllers\Controller;
use App\Services\Pos\PosOrderService;
use App\Services\Kds\TableOrderProgress;
use App\Services\Kds\FreeKitchenAssistant;
use App\Services\Platform\FeatureRegistry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class KdsController extends Controller
{
    public function __construct(private readonly PosOrderService $pos) {}

    public function index(Request $request): View
    {
        $branches = DB::table('branches as br')
            ->join('businesses as bu', 'bu.id', '=', 'br.business_id')
            ->join('vendors as ve', 've.id', '=', 'bu.vendor_id')
            ->where('ve.user_id', $request->user()->id)
            ->whereNull('br.deleted_at')->whereNull('bu.deleted_at')->whereNull('ve.deleted_at')
            ->whereIn('bu.business_type', PosOrderService::BUSINESS_TYPES)
            ->select('br.id', 'br.name', 'bu.name as business_name')
            ->orderBy('br.name')->get();
        return view('kds.index', compact('branches'));
    }

    public function showBranch(Request $request, int $branch): View
    {
        $branchRecord = $this->pos->ownedBranch($request, $branch);
        abort_unless(in_array($branchRecord->business_type, PosOrderService::BUSINESS_TYPES, true), 404);
        $orders = DB::table('pos_orders as po')->leftJoin('restaurant_dining_tables as t','t.id','=','po.dining_table_id')
            ->where('po.branch_id', $branch)
            ->whereIn('po.status', ['received', 'preparing', 'ready'])
            ->select('po.*','t.label as dining_table_label')
            ->orderBy('po.placed_at')->limit(100)->get();
        $items = DB::table('pos_order_items')->whereIn('order_id', $orders->pluck('id'))
            ->orderBy('id')->get()->groupBy('order_id');
        $freeAiEnabled = app(FeatureRegistry::class)->isEnabled('kds.free_assistant');
        return view('kds.branch', compact('branchRecord', 'orders', 'items', 'freeAiEnabled'));
    }

    public function itemProgress(Request $request, int $item, TableOrderProgress $progress): RedirectResponse
    {
        $data=$request->validate(['action'=>['required','in:add,ready,served'],
            'quantity'=>['required','integer','min:1','max:100']]);
        $progress->change($request,$item,$data['action'],(int)$data['quantity']);
        return back()->with('success','Updated the existing order and recorded the change.');
    }

    public function tableSummary(Request $request, int $branch, int $table, TableOrderProgress $progress): JsonResponse
    {
        return response()->json($progress->table($request,$branch,$table),200,
            ['Cache-Control'=>'no-store, private']);
    }

    /** Safe Free Mode: answer a question about this vendor's single existing order. */
    public function freeAsk(Request $request, int $order, FreeKitchenAssistant $assistant): RedirectResponse
    {
        abort_unless(app(FeatureRegistry::class)->isEnabled('kds.free_assistant'), 404);
        $data = $request->validate(['question'=>['required','string','min:2','max:300']]);
        $row = DB::table('pos_orders as o')
            ->leftJoin('restaurant_dining_tables as t','t.id','=','o.dining_table_id')
            ->where('o.id', $order)
            ->select('o.id','o.branch_id','o.status','t.label as table_label')->first();
        abort_unless($row, 404);
        $branch = $this->pos->ownedBranch($request, (int) $row->branch_id);
        $this->pos->ensureApproved($branch);
        abort_if($row->status === 'cancelled', 422, 'Cancelled order.');

        $items = DB::table('pos_order_items')->where('order_id',$order)->orderBy('id')->get();
        $snapshot = [
            'table'=>$row->table_label ?: 'Counter',
            'items'=>$items->map(fn ($i) => [
                'name'=>(string)$i->name_snapshot,
                'ordered'=>(int)$i->quantity,
                'added'=>(int)$i->added_quantity,
                'ready'=>(int)$i->ready_quantity,
                'served'=>(int)$i->served_quantity,
                'remaining_to_prepare'=>max(0,(int)$i->quantity-(int)$i->ready_quantity),
            ])->all(),
        ];
        $reply=$assistant->answer($data['question'],$snapshot);
        return back()->with('free_kds_reply',[
            'order_id'=>$order,'reply'=>$reply['reply'],
            'resolved'=>$reply['resolved'],
            'needs_confirmation'=>$reply['needs_confirmation'],
        ]);
    }

    public function updateStatus(Request $request, int $order): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', 'in:preparing,ready,served']]);
        DB::transaction(function () use ($request, $order, $data): void {
            $row = DB::table('pos_orders')->where('id', $order)->lockForUpdate()->first();
            abort_unless($row, 404);
            $branch = $this->pos->ownedBranch($request, (int) $row->branch_id, true);
            $this->pos->ensureApproved($branch);
            $next = ['received' => 'preparing', 'preparing' => 'ready', 'ready' => 'served'];
            if (($next[$row->status] ?? null) !== $data['status']) {
                throw ValidationException::withMessages(['status' => 'Invalid Kitchen status transition.']);
            }
            if ($data['status'] === 'ready') {
                DB::table('pos_order_items')->where('order_id',$order)->update([
                    'ready_quantity'=>DB::raw('quantity'),'updated_at'=>now(),
                ]);
            } elseif ($data['status'] === 'served') {
                DB::table('pos_order_items')->where('order_id',$order)->update([
                    'ready_quantity'=>DB::raw('quantity'),
                    'served_quantity'=>DB::raw('quantity'),'updated_at'=>now(),
                ]);
            }
            DB::table('pos_orders')->where('id', $order)->update([
                'status' => $data['status'], 'updated_at' => now(),
            ]);
            $this->pos->audit($request, 'kds.order.status_changed', 'pos_order', $order,
                ['status' => $data['status']], ['status' => $row->status]);
        });
        return back()->with('success', 'Kitchen order updated.');
    }
}
