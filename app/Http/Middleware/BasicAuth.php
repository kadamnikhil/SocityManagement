<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class BasicAuth
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->authtoken) {
            $authtoken = '1234';
            if ($request->authtoken != $authtoken) {
                return response()->json(['message' => 'Authentication failed'], 401);
            }
        } else {
            return response()->json(['message' => 'Authentication failed'], 401);
        }
        return $next($request);
    }
}