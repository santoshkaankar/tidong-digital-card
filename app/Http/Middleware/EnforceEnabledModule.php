<?php

namespace App\Http\Middleware;

use App\Services\Platform\ModuleRegistry;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnforceEnabledModule
{
    public function handle(Request $request, Closure $next): Response
    {
        $registry = app(ModuleRegistry::class);
        foreach ($registry->catalog() as $code => $module) {
            foreach (($module['paths'] ?? []) as $path) {
                if (! $request->is($path)) {
                    continue;
                }
                // Admin can still inspect historic Delivery data while disabled;
                // new partner/dispatch/status changes remain blocked.
                if ($code === 'delivery' && $request->is('admin/delivery', 'admin/delivery/*', 'staff-admin/delivery', 'staff-admin/delivery/*', 'employee/delivery', 'employee/delivery/*')
                    && $request->isMethod('GET')) {
                    return $next($request);
                }
                abort_unless($registry->isEnabled($code), 503, 'This module is currently disabled.');
                return $next($request);
            }
        }
        return $next($request);
    }
}
