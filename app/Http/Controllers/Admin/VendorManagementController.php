<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class VendorManagementController extends Controller
{
    private function basePath(): string
    {
        $segment = request()->segment(1);

        abort_unless(
            in_array($segment, ['admin', 'staff-admin', 'employee'], true),
            404
        );

        return '/' . $segment . '/vendors';
    }

    private function vendorQuery()
    {
        return DB::table('vendors')
            ->leftJoin('users', 'users.id', '=', 'vendors.user_id')
            ->whereNull('vendors.deleted_at')
            ->select(
                'vendors.*',
                'users.name as user_name',
                'users.email as user_email'
            );
    }

    public function index(Request $request)
    {
        $query = $this->vendorQuery();

        if ($request->filled('search')) {
            $term = trim((string) $request->input('search'));

            $query->where(function ($q) use ($term) {
                $q->where('vendors.display_name', 'like', '%' . $term . '%')
                    ->orWhere('vendors.vendor_code', 'like', '%' . $term . '%')
                    ->orWhere('users.name', 'like', '%' . $term . '%')
                    ->orWhere('users.email', 'like', '%' . $term . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('vendors.status', $request->input('status'));
        }

        $vendors = $query
            ->orderByDesc('vendors.id')
            ->paginate(15)
            ->withQueryString();

        $basePath = $this->basePath();

        return view('vendor-management.index', compact('vendors', 'basePath'));
    }

    public function show(int $vendor)
    {
        $vendorRecord = $this->vendorQuery()
            ->where('vendors.id', $vendor)
            ->first();

        abort_unless($vendorRecord, 404);

        $businesses = collect();

        if (Schema::hasTable('businesses')) {
            $businesses = DB::table('businesses')
                ->where('vendor_id', $vendor)
                ->whereNull('deleted_at')
                ->orderByDesc('id')
                ->get();
        }

        $basePath = $this->basePath();
        $canApprove = request()->user()->hasPlatformPermission('vendors.approve');
        $canReject = request()->user()->hasPlatformPermission('vendors.reject');

        $registry = app(\App\Services\Platform\ModuleRegistry::class);
        $vendorModules = array_map(function ($module) use ($registry, $vendor) {
            $module['enabled'] = $registry->isEnabledForVendor($module['code'], $vendor);
            return $module;
        }, array_filter($registry->overview(), fn ($module) => $module['installed'] && in_array($module['code'], ['delivery', 'pos', 'kds', 'sublimation', 'grocery', 'legal_advisory'], true)));
        return view(
            'vendor-management.show',
            compact('vendorRecord', 'businesses', 'basePath', 'canApprove', 'canReject', 'vendorModules')
        );
    }

    public function setModule(Request $request, int $vendor, string $code)
    {
        abort_unless(DB::table('vendors')->where('id', $vendor)->whereNull('deleted_at')->exists(), 404);
        $data = $request->validate(['enabled' => ['required', 'boolean']]);
        app(\App\Services\Platform\ModuleRegistry::class)->setVendorEnabled($code, $vendor, (bool) $data['enabled'], (int) $request->user()->id);
        return back()->with('success', 'Vendor service updated.');
    }

    public function approve(Request $request, int $vendor)
    {
        $data = $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        return $this->changeStatus(
            $request,
            $vendor,
            'approved',
            $data['reason'] ?? null
        );
    }

    public function reject(Request $request, int $vendor)
    {
        $data = $request->validate([
            'reason' => ['required', 'string', 'min:5', 'max:500'],
        ]);

        return $this->changeStatus(
            $request,
            $vendor,
            'rejected',
            $data['reason']
        );
    }

    private function changeStatus(
        Request $request,
        int $vendorId,
        string $newStatus,
        ?string $reason
    ) {
        $actor = $request->user();

        abort_unless($actor, 403);

        DB::transaction(function () use (
            $request,
            $actor,
            $vendorId,
            $newStatus,
            $reason
        ) {
            $vendor = DB::table('vendors')
                ->where('id', $vendorId)
                ->whereNull('deleted_at')
                ->lockForUpdate()
                ->first();

            abort_unless($vendor, 404);

            if ($vendor->status !== 'pending') {
                throw ValidationException::withMessages([
                    'status' => 'Sirf pending Vendor ko approve ya reject kiya ja sakta hai.',
                ]);
            }

            $oldValues = ['status' => $vendor->status];
            $newValues = ['status' => $newStatus];

            $update = [
                'status' => $newStatus,
                'updated_at' => now(),
            ];

            if ($newStatus === 'approved') {
                if (Schema::hasColumn('vendors', 'approved_at')) {
                    $update['approved_at'] = now();
                    $newValues['approved_at'] = now()->toDateTimeString();
                }

                if (Schema::hasColumn('vendors', 'approved_by')) {
                    $update['approved_by'] = $actor->id;
                    $newValues['approved_by'] = $actor->id;
                }
            }

            DB::table('vendors')
                ->where('id', $vendorId)
                ->update($update);

            if (Schema::hasTable('audit_logs')) {
                $available = Schema::getColumnListing('audit_logs');

                $audit = [
                    'actor_user_id' => $actor->id,
                    'action' => 'vendor.' . $newStatus,
                    'entity_type' => 'vendor',
                    'entity_id' => $vendorId,
                    'reason' => $reason,
                    'old_values' => json_encode($oldValues),
                    'new_values' => json_encode($newValues),
                    'metadata' => json_encode([
                        'vendor_code' => $vendor->vendor_code,
                        'request_path' => $request->path(),
                    ]),
                    'ip_address' => $request->ip(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $audit = array_intersect_key($audit, array_flip($available));

                if (isset($audit['action'], $audit['actor_user_id'])) {
                    DB::table('audit_logs')->insert($audit);
                }
            }
        });

        return redirect()->route(request()->segment(1) . '.vendors.show', ['vendor' => $vendorId])
            ->with('success', 'Vendor status successfully updated.');
    }
}