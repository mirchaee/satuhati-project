<?php
// app/Http/Controllers/Auth/AuthController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SyncData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    // ════════════════════════════════════════
    //  REGISTER
    // ════════════════════════════════════════

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $rules = [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role'     => 'required|string|in:suami,istri',
            'phone'    => 'nullable|string|max:20',
        ];

        // KEMBALIKAN VALIDASI KHUSUS ISTRI (Jangan Dihapus)
        if ($request->role === 'istri') {
            $rules['pregnancy_week'] = 'required|integer|min:1|max:42';
            $rules['hpht']           = 'required|date|before:today';
        }

        $data = $request->validate($rules);

        // Buat user baru dengan data yang lengkap
        $user = \App\Models\User::create([
            'name'           => $data['name'],
            'email'          => $data['email'],
            'password'       => \Illuminate\Support\Facades\Hash::make($data['password']),
            'role'           => $data['role'],
            'phone'          => $data['phone'] ?? null,
            'pregnancy_week' => $data['role'] === 'istri' ? $data['pregnancy_week'] : null,
            'hpht'           => $data['role'] === 'istri' ? $data['hpht'] : null,
        ]);

        \Illuminate\Support\Facades\Auth::login($user);

        return redirect()->route('dashboard');
    }

    // ════════════════════════════════════════
    //  LOGIN
    // ════════════════════════════════════════

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors([
                'email' => 'Email atau password yang kamu masukkan salah.',
            ]);
    }

    // ════════════════════════════════════════
    //  LOGOUT
    // ════════════════════════════════════════

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}