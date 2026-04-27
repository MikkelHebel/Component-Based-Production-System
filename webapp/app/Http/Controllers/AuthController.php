<?php

namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function show()
    {
        return view('auth.login');
    }

    public function authenticate(Request $request) : RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            // Protect against session fixation
            $request->session()->regenerate();

            // Authentication successful, redirect to the intended page
            // intended make sure to route if the user is authenticated
            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'login-error' => 'The provided credentials do not match our records',
        ])->onlyInput('email'); // Keeps the email filled in
    }

    public function logout(Request $request) : RedirectResponse 
    {
        Auth::logout();

        // Invalidate session
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
