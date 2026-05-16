<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Token
{
    public function handle(Request $request, Closure $next)
    {
        if (!\Auth::guard('api')->check()) {
            return response()->json(['message' => 'User not logged in'], 401);
        } else {
            return $next($request);
        }

    }
}