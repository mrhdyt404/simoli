<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller
{
    /**
     * Show login form
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return Auth::user()->isFieldUser() ? redirect('/operator') : redirect('/dashboard');
        }

        return view('auth.login');
    }

    /**
     * Handle login request
     * Old system uses plain text passwords, so we do manual check
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Cek apakah username ada
        $userByUsername = User::where('username', $request->username)->first();

        // Cek apakah password ada di database
        $userByPassword = User::where('password', $request->password)->first();

        // Username dan Password benar
        if ($userByUsername && $userByUsername->password === $request->password) {

            Auth::login($userByUsername, $request->filled('remember'));
            $request->session()->regenerate();

            if ($userByUsername->isFieldUser()) {
                return redirect()->route('operator.index');
            }

            return redirect()->intended('/dashboard');
        }

        // Username salah, Password benar
        if (!$userByUsername && $userByPassword) {

            return back()->withErrors([
                'login' => 'Username tidak ditemukan.',
            ])->withInput($request->only('username'));
        }

        // Username benar, Password salah
        if ($userByUsername && $userByUsername->password !== $request->password) {

            return back()->withErrors([
                'login' => 'Password yang Anda masukkan salah.',
            ])->withInput($request->only('username'));
        }

        // Username dan Password sama-sama salah
        return back()->withErrors([
            'login' => 'Username tidak ditemukan.',
        ])->withInput($request->only('username'));
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
