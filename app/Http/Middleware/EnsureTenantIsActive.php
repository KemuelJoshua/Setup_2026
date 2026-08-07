<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantIsActive
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! tenancy()->initialized) {
            return $next($request);
        }

        abort_unless((bool) tenant('is_active'), Response::HTTP_FORBIDDEN, 'This school is inactive.');

        return $next($request);
    }
}
