<?php
namespace App\Http\Middleware;
use App\Services\Platform\ModuleRegistry;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
final class EnsureAffiliateModuleEnabled {
    public function handle(Request $request, Closure $next): Response {
        abort_unless(app(ModuleRegistry::class)->isEnabled('affiliate'), 404);
        return $next($request);
    }
}
