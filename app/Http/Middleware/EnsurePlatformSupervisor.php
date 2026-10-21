<?php

namespace App\Http\Middleware;

use App\Services\Platform\PlatformCapability;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePlatformSupervisor
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) return redirect()->guest(route('login'));
        abort_unless(app(PlatformCapability::class)->isSupervisor($request->user()), 403);
        return $next($request);
    }
}
