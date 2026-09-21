<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        $request->validate([
            'email' => ['required'],
            'password' => ['required'],
        ]);

        $login = $request->input('email');
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'nip';

        $credentials = [
            $field => $login,
            'password' => $request->input('password'),
        ];

        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            // Check if user is active
            if (! Auth::user()->is_active) {
                AuditLog::record('LOGIN_BLOCKED_INACTIVE', 'User', Auth::id(), null, [
                    'reason' => 'User account is inactive',
                    'login_field' => $field,
                ]);

                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Akun Anda telah dinonaktifkan. Silakan hubungi Administrator.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();

            // Record successful login in Audit Log
            AuditLog::record('LOGIN_SUCCESS', 'User', Auth::id(), null, [
                'login_field' => $field,
            ]);

            return redirect()->intended('dashboard');
        }

        AuditLog::record('LOGIN_FAILED', 'User', null, null, [
            'login' => $login,
            'reason' => 'Invalid credentials',
        ]);

        return back()->withErrors([
            'email' => 'ID Pengguna / Email atau kata sandi tidak cocok dengan data kami.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            AuditLog::record('LOGOUT', 'User', Auth::id());
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
