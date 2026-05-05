<?php

namespace App\Http\Middleware;

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
            return app(InitializeTenancyBySubdomain::class)->handle($request, $next);
        }
    }
}
