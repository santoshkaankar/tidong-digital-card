<?php

namespace App\Http\Middleware;

use App\Services\Platform\PlatformCapability;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePlatformCapability
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (! $request->user()) return redirect()->guest(route('login'));
        abort_unless(app(PlatformCapability::class)->allows($request->user(), $permission), 403);
        return $next($request);
    }
}
