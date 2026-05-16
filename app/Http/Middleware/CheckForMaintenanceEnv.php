<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckForMaintenanceEnv
{
    public function handle(Request $request, Closure $next)
    {
        $env = env('APP_ENV');

        if ($env === 'maintenance') {
            // Show normal maintenance page
            return response()->view('maintenance');
        }

        if ($env === 'admin') {
            // Show admin maintenance page
            return response()->view('admin_maintenance');
        }
        return $next($request);
    }
}
