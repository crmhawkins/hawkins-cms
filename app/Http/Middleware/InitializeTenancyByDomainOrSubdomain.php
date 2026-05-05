<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Resolvers\DomainTenantResolver;

class InitializeTenancyByDomainOrSubdomain
{
    public function handle(Request $request, Closure $next)
    {
        try {
            return app(InitializeTenancyByDomain::class)->handle($request, $next);
        } catch (\Exception $e) {
            try {
                return app(InitializeTenancyBySubdomain::class)->handle($request, $next);
            } catch (\Exception $e2) {
                // En entorno local, usar el primer tenant disponible como fallback
                if (app()->environment('local')) {
                    $tenant = Tenant::first();
                    if ($tenant) {
                        tenancy()->initialize($tenant);
                        return $next($request);
                    }
                }
                abort(404, 'Tenant not found');
            }
        }
    }
}
