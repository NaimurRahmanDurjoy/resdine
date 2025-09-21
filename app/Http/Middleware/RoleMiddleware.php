<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role, $guard = null)
    {
        $guard = $guard ?? 'web'; // default guard
        $user = Auth::guard($guard)->user();

        // Not logged in
        if (!$user) {
            return redirect()->route('login');
        }

        // User role relationship check
        if (!$user->role || $user->role->name !== $role) {
            // Unauthorized redirect
            return redirect()->route('unauthorized'); 
        }

        return $next($request);
    }
}
