<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Cek apakah role user ada di daftar yang diizinkan
        if (!in_array(Auth::user()->role, $roles)) {
            abort(403, 'Akses ditolak. Halaman ini tidak tersedia untuk peranmu.');
        }

        return $next($request);
    }
}