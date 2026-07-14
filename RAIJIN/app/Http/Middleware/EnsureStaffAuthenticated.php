<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStaffAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->has('staff')) {
            return redirect()->guest('/login');
        }

        return $next($request);
    }
}
