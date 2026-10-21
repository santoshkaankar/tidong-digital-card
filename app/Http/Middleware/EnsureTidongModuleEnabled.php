<?php
namespace App\Http\Middleware;
use App\Services\Platform\ModuleRegistry;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
final class EnsureTidongModuleEnabled {
    public function handle(Request $request, Closure $next, string $module): Response {
        abort_unless(app(ModuleRegistry::class)->isEnabled($module), 404);
        return $next($request);
    }
}
