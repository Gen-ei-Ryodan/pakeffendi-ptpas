<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStaffGuest
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->session()->has('staff')) {
            return redirect('/');
        }

        return $next($request);
    }
}
