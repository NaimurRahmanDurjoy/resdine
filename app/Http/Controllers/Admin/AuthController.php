<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

use Inertia\Inertia;

class AuthController extends Controller
{
    public function showLoginForm()
    { 
        return Inertia::render('Admin/Login');
    }

    public function login(Request $request)
    { 
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $key = 'login-attempts:' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) { // 5 attempts max
            throw ValidationException::withMessages([
                'email' => ['Too many login attempts. Please try again later.'],
            ]);
        }

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            RateLimiter::clear($key); // Clear attempts on successful login
    
            // Use intended URL if exists, otherwise role-based dashboard
            return redirect()->to(Auth::user()->redirectToDashboard());
        }
        RateLimiter::hit($key, 60); // Block for 60 seconds per attempt

        return back()->with('error', 'Invalid credentials.');

    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
