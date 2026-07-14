<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureGuestLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is logged in as Customer
        if (Auth::guard('customer')->check()) {
            return $next($request);
        }

        // Check if user is logged in as User (Sales)
        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            
            // Allow Sales
            if ($user->role === 'sales') {
                return $next($request);
            }
            
            // If Admin tries to access Guest routes, treat as Guest (not logged in for this context)
            if ($user->isAdmin()) {
                // Do not redirect to admin dashboard.
                // Instead, let them login as Buyer/Sales if they want to access Guest features.
                // This effectively ignores the Admin session for Guest routes.
            }
        }

        // For AJAX/JSON requests, return 401 instead of redirecting
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Not logged in (or logged in as Admin but accessing Guest area)
        // Store intended URL for redirect back after login
        if (!$request->is('logout')) {
            $request->session()->put('url.intended', $request->url());
        }

        return redirect()->route('guest.login')->with('info', 'Silakan login sebagai Pembeli atau Sales untuk mengakses halaman ini.');
    }
}
