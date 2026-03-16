<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityLogMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Only log state-changing requests (POST, PUT, PATCH, DELETE) for authenticated admins
        if (Auth::guard('admin')->check() && in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            ActivityLog::create([
                'user_id' => Auth::guard('admin')->id(),
                'action' => $request->method(),
                'module' => $this->getModuleName($request),
                'payload' => $request->except(['password', 'password_confirmation', '_token']),
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent')
            ]);
        }

        return $response;
    }

    protected function getModuleName(Request $request)
    {
        $segments = $request->segments();
        return isset($segments[1]) ? $segments[1] : 'unknown';
    }
}
