<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, ...$guards)
    {
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // Eager-load to prevent lazy queries in redirectToDashboard()
                $user = Auth::guard($guard)->user()->load('role.landingMenu');
                return redirect()->to($user->redirectToDashboard());
            }
        }

        return $next($request);
    }
}
