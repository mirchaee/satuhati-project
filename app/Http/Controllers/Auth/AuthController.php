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
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'role'     => 'required|in:istri,suami',
            'phone'    => 'nullable|string|max:20',
        ];

        // Validasi tambahan khusus istri
        if ($request->role === 'istri') {
            $rules['pregnancy_week'] = 'required|integer|min:1|max:42';
            $rules['hpht']           = 'required|date|before:today';
        }

        $data = $request->validate($rules);

        // Buat user baru
        $user = DB::transaction(function () use ($data) {
            // 1. Buat user baru
            $newUser = User::create([
                'name'           => $data['name'],
                'email'          => $data['email'],
                'password'       => Hash::make($data['password']),
                'role'           => $data['role'],
                'phone'          => $data['phone'] ?? null,
                'pregnancy_week' => $data['pregnancy_week'] ?? null,
                'hpht'           => $data['hpht'] ?? null,
            ]);

            // 2. Buat SyncData khusus istri
            if ($newUser->role === 'istri') {
                SyncData::create([
                    'wife_id'      => $newUser->id,
                    'husband_id'   => null,
                    'pairing_code' => SyncData::generateCode(),
                    'status'       => false,
                ]);
            }

            return $newUser;
        });

        // Langsung loginkan setelah transaksi berhasil
        Auth::login($user);

        return redirect()->route('dashboard')
                         ->with('success', "Selamat datang, {$user->name}! 🌸");
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