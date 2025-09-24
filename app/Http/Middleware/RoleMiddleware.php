<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle($request, Closure $next, string $role)
    {
        $user = Auth::user();

        // Make sure user is logged in
        if (!$user) {
            return redirect()->route('login')->with('error', 'You must be logged in.');
        }

        // Check if role exists and matches
        if (!$user->role || $user->role->name !== $role) {
            abort(403, 'Unauthorized: Requires ' . $role . ' role.');
        }

        return $next($request);
    }
}
