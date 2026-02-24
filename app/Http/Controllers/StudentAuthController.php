<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class StudentAuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check() && Auth::user()?->role === 'student') {
            return redirect()->route('landing');
        }

        return view('student.auth.login');
    }

    public function showRegister()
    {
        if (Auth::check() && Auth::user()?->role === 'student') {
            return redirect()->route('landing');
        }

        return view('student.auth.register');
    }

    public function register(Request $request)
    {
        if (Auth::check() && Auth::user()?->role !== 'student') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'class_group' => ['nullable', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:180', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $student = User::create([
            'name' => $validated['name'],
            'class_group' => $validated['class_group'] ?? null,
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => 'student',
            'points' => 0,
            'streak_days' => 0,
        ]);

        Auth::login($student);
        $request->session()->regenerate();

        return redirect()->route('landing')->with('success', 'Akun murid berhasil dibuat. Selamat bermain dan belajar.');
    }

    public function login(Request $request)
    {
        if (Auth::check() && Auth::user()?->role !== 'student') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        } elseif (Auth::check() && Auth::user()?->role === 'student') {
            return redirect()->route('landing')->with('success', 'Kamu sudah login sebagai murid.');
        }

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $attemptPayload = [
            'email' => $credentials['email'],
            'password' => $credentials['password'],
            'role' => 'student',
        ];

        if (!Auth::attempt($attemptPayload, $request->boolean('remember'))) {
            $existingUser = User::query()->where('email', $credentials['email'])->first();
            if ($existingUser && Hash::check($credentials['password'], $existingUser->password) && $existingUser->role !== 'student') {
                return back()->withErrors([
                    'email' => 'Akun ini bukan akun murid. Gunakan login admin jika kamu admin.',
                ])->onlyInput('email');
            }

            return back()->withErrors([
                'email' => 'Email/password murid tidak cocok.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->route('landing')->with('success', 'Selamat datang kembali.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('landing');
    }
}
