<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);
        $credentials['email'] = mb_strtolower(trim($credentials['email']));

        $attemptPayload = [
            'email' => $credentials['email'],
            'password' => $credentials['password'],
            'role' => 'admin',
            'is_active' => true,
        ];

        if (!Auth::attempt($attemptPayload, $request->boolean('remember'))) {
            $fallbackPayload = [
                'email' => $credentials['email'],
                'password' => $credentials['password'],
                'role' => 'super_admin',
                'is_active' => true,
            ];

            if (Auth::attempt($fallbackPayload, $request->boolean('remember'))) {
                $request->session()->regenerate();
                $request->user()?->forceFill(['last_login_at' => now()])->save();

                return redirect()->route('admin.dashboard');
            }

            $editorPayload = [
                'email' => $credentials['email'],
                'password' => $credentials['password'],
                'role' => 'editor',
                'is_active' => true,
            ];

            if (Auth::attempt($editorPayload, $request->boolean('remember'))) {
                $request->session()->regenerate();
                $request->user()?->forceFill(['last_login_at' => now()])->save();

                return redirect()->route('admin.dashboard');
            }

            return back()->withErrors([
                'email' => 'Email atau password salah.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();
        $request->user()?->forceFill(['last_login_at' => now()])->save();

        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
