<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class LoginController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'=>'required|email',
            'password'=>'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors(['email'=>'Invalid credentials']);
        }

        session(['user_id' => $user->id, 'user_role' => $user->role_id]);
        return redirect()->route($user->role.'.dashboard'); // admin or cashier
    }

    public function logout()
    {
        session()->flush();
        return redirect()->route('login');
    }
}
