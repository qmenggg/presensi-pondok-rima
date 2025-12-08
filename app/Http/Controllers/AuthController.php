<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        return view('pages.auth.signin', ['title' => 'Sign In']);
    }

    /**
     * Handle the login request.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Check if user exists and is not a santri
        $user = \App\Models\User::where('username', $credentials['username'])->first();

        if ($user && $user->role === 'santri') {
            return back()
                ->withInput($request->only('username'))
                ->withErrors(['username' => 'Akun santri tidak memiliki akses login ke sistem ini.']);
        }

        // Check if user is active
        if ($user && !$user->aktif) {
            return back()
                ->withInput($request->only('username'))
                ->withErrors(['username' => 'Akun Anda telah dinonaktifkan. Hubungi admin.']);
        }

        // Attempt to login
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('/');
        }

        return back()
            ->withInput($request->only('username'))
            ->withErrors(['username' => 'Username atau password salah.']);
    }

    /**
     * Handle the logout request.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
