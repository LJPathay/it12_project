<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('admin')->check()) {
            // If user is logged in as another role, show unauthorized instead of login redirect
            if (Auth::guard('patient')->check() || Auth::guard('super_admin')->check() || Auth::check()) {
                return redirect()->back();
            }
            return redirect('/login')->with('error', 'Please login as an admin to access this page.');
        }

        return $next($request);
    }
}
