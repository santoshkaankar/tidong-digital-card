<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class BusinessManagementController extends Controller
{
    private function basePath(): string
    {
        $segment = request()->segment(1);
        abort_unless(in_array($segment, ['admin', 'staff-admin', 'employee'], true), 404);

        return '/'.$segment.'/businesses';
    }

    private function query()
    {
        return DB::table('businesses')
            ->join('vendors', 'businesses.vendor_id', '=', 'vendors.id')
            ->leftJoin('users', 'vendors.user_id', '=', 'users.id')
            ->whereNull('businesses.deleted_at')
            ->whereNull('vendors.deleted_at')
            ->select(
                'businesses.*',
                'vendors.display_name as vendor_name',
                'vendors.vendor_code',
                'vendors.status as vendor_status',
                'users.email as vendor_email'
            );
    }

    public function index(Request $request): View
    {
        $data = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:pending,approved,rejected,suspended'],
        ]);

        $query = $this->query();
        if (! empty($data['search'])) {
            $term = trim($data['search']);
            $query->where(function ($builder) use ($term): void {
                $builder->where('businesses.name', 'like', '%'.$term.'%')
                    ->orWhere('businesses.business_type', 'like', '%'.$term.'%')
                    ->orWhere('vendors.display_name', 'like', '%'.$term.'%')
                    ->orWhere('vendors.vendor_code', 'like', '%'.$term.'%');
            });
        }
        if (! empty($data['status'])) {
            $query->where('businesses.status', $data['status']);
        }

        $businesses = $query->orderByDesc('businesses.id')->paginate(15)->withQueryString();
        $basePath = $this->basePath();

        return view('business-management.index', compact('businesses', 'basePath'));
    }

    public function show(Request $request, int $business): View
    {
        $businessRecord = $this->query()->where('businesses.id', $business)->first();
        abort_unless($businessRecord, 404);

        $branches = DB::table('branches')
            ->where('business_id', $business)
            ->whereNull('deleted_at')
            ->orderBy('id')
            ->get();

        $basePath = $this->basePath();
        $canApprove = $request->user()->hasPlatformPermission('businesses.approve');
        $canReject = $request->user()->hasPlatformPermission('businesses.reject');

        return view('business-management.show', compact(
            'businessRecord', 'branches', 'basePath', 'canApprove', 'canReject'
        ));
    }

    public function approve(Request $request, int $business): RedirectResponse
    {
        $data = $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        return $this->changeStatus($request, $business, 'approved', $data['reason'] ?? null);
    }

    public function reject(Request $request, int $business): RedirectResponse
    {
        $data = $request->validate([
            'reason' => ['required', 'string', 'min:5', 'max:500'],
        ]);

        return $this->changeStatus($request, $business, 'rejected', $data['reason']);
    }

    private function changeStatus(
        Request $request,
        int $businessId,
        string $status,
        ?string $reason
    ): RedirectResponse {
        DB::transaction(function () use ($request, $businessId, $status, $reason): void {
            $business = DB::table('businesses')
                ->where('id', $businessId)
                ->whereNull('deleted_at')
                ->lockForUpdate()
                ->first();
            abort_unless($business, 404);

            if ($business->status !== 'pending') {
                throw ValidationException::withMessages([
                    'status' => 'Only pending Businesses can be approved or rejected.',
                ]);
            }

            $vendor = DB::table('vendors')
                ->where('id', $business->vendor_id)
                ->whereNull('deleted_at')
                ->first();
            abort_unless($vendor, 404);

            if ($status === 'approved' && $vendor->status !== 'approved') {
                throw ValidationException::withMessages([
                    'vendor' => 'Approve the Vendor before approving its Business.',
                ]);
            }

            // Approval is NOT permission to accept orders: POS, staff, and branch
            // readiness must be checked by a separate future activation workflow.
            DB::table('businesses')->where('id', $businessId)->update([
                'status' => $status,
                'is_accepting_orders' => false,
                'updated_at' => now(),
            ]);

            // No branch can be ready for orders before activation.
            DB::table('branches')->where('business_id', $businessId)
                ->whereNull('deleted_at')
                ->update(['is_accepting_orders' => false, 'updated_at' => now()]);

            DB::table('audit_logs')->insert([
                'actor_user_id' => $request->user()->id,
                'action' => 'business.'.$status,
                'entity_type' => 'business',
                'entity_id' => $businessId,
                'reason' => $reason,
                'old_values' => json_encode([
                    'status' => $business->status,
                    'is_accepting_orders' => (bool) $business->is_accepting_orders,
                ]),
                'new_values' => json_encode([
                    'status' => $status,
                    'is_accepting_orders' => false,
                ]),
                'metadata' => json_encode([
                    'vendor_id' => $vendor->id,
                    'vendor_code' => $vendor->vendor_code,
                    'request_path' => $request->path(),
                ]),
                'ip_address' => $request->ip(),
                'created_at' => now(),
            ]);
        });

        return redirect()->route(request()->segment(1) . '.businesses.show', ['business' => $businessId])
            ->with('success', 'Business '.($status === 'approved' ? 'approved' : 'rejected').'. Orders remain disabled.');
    }
}
