<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Symfony\Component\HttpFoundation\Response;

class InitializeTenancyByDomainOrCentral
{
    public function handle(Request $request, Closure $next): Response
    {
        $centralDomains = config('tenancy.central_domains', []);

        if (in_array($request->getHost(), $centralDomains, true)) {
            config([
                'session.connection' => 'central',
            ]);

            return $next($request);
        }

        return app(InitializeTenancyByDomain::class)->handle(
            $request,
            function (Request $request) use ($next): Response {
                // Tenancy is initialized at this point.
                config([
                    'session.connection' => 'tenant',
                ]);

                logger()->info('SESSION CONNECTION BEFORE WEB', [
                    'host' => $request->getHost(),
                    'tenant' => tenant('id'),
                    'default_connection' => config('database.default'),
                    'session_connection' => config('session.connection'),
                ]);
                return $next($request);
            }
        );
    }
}
