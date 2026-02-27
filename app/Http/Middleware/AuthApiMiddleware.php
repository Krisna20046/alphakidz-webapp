<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthApiMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!session('token')) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('login');
        }

        return $next($request);
    }
}