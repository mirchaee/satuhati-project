<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPaired
{
    // Middleware ini memastikan user sudah paired sebelum akses fitur tertentu
    public function handle(Request $request, Closure $next): mixed
    {
        if (!Auth::user()->isPaired()) {
            return redirect()
                ->route('sync.index')
                ->with('warning', 'Hubungkan akun dengan pasanganmu terlebih dahulu.');
        }

        return $next($request);
    }
}