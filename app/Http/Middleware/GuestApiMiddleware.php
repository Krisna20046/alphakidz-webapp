<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class GuestApiMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (session('token')) {
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}