<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm(): \Illuminate\View\View
    {
        $rememberedEmail = request()->cookie('remembered_email', '');

        return view('auth.login', compact('rememberedEmail'));
    }

    /**
     * Handle a login request.
     */
    public function login(Request $request): \Illuminate\Http\RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        if (auth()->attempt($credentials, $remember)) {
            $request->session()->regenerate();

            if ($remember) {
                cookie()->queue(cookie()->forever('remembered_email', $request->email));
            } else {
                cookie()->queue(cookie()->forget('remembered_email'));
            }

            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Log the user out.
     */
    public function logout(Request $request)
    {
        auth()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
