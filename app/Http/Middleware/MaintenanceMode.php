<?php
namespace App\Http\Middleware;

use App\Models\SiteSettings;
use Closure;
use Illuminate\Http\Request;

class MaintenanceMode
{
    public function handle(Request $request, Closure $next)
    {
        // No aplicar a rutas de admin
        if ($request->is('admin*') || $request->is('login*')) {
            return $next($request);
        }

        $settings = SiteSettings::instance();

        if ($settings->maintenance_mode) {
            return response()->view('maintenance', ['message' => $settings->maintenance_message], 503);
        }

        return $next($request);
    }
}
