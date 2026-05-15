<?php
namespace App\Http\Middleware;

use App\Models\Redirect;
use Closure;
use Illuminate\Http\Request;

class HandleRedirects
{
    public function handle(Request $request, Closure $next)
    {
        $path = '/' . ltrim($request->getPathInfo(), '/');

        $redirect = Redirect::where('from_url', $path)
            ->where('active', true)
            ->first();

        if ($redirect) {
            return redirect($redirect->to_url, $redirect->status_code);
        }

        return $next($request);
    }
}
