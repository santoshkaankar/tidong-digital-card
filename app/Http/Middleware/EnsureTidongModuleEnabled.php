<?php
namespace App\Http\Middleware;
use App\Services\Platform\ModuleRegistry;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
final class EnsureTidongModuleEnabled {
    public function handle(Request $request, Closure $next, string $module): Response {
        $registry = app(ModuleRegistry::class);
        if ($request->user()) {
            $vendorId = DB::table('vendors')->where('user_id', $request->user()->id)
                ->whereNull('deleted_at')->value('id');
            if ($vendorId) {
                abort_unless($registry->isEnabledForVendor($module, (int) $vendorId), 403);
                return $next($request);
            }
        }
        abort_unless($registry->isEnabled($module), 404);
        return $next($request);
    }
}
