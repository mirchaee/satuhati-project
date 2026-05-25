<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\SyncController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth; // TAMBAHKAN INI
use Illuminate\Support\Facades\Broadcast; // TAMBAHKAN INI

// ── Root ────────────────────────────────────────
Route::get('/', function () {
    return view('landing');
});

// ── Auth (hanya untuk tamu / belum login) ───────
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])
         ->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/login', [AuthController::class, 'showLogin'])
         ->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// ── Logout ──────────────────────────────────────
Route::post('/logout', [AuthController::class, 'logout'])
     ->name('logout')
     ->middleware('auth');

// ── Protected (harus login) ─────────────────────
Route::middleware('auth')->group(function () {

    // Dashboard — controller akan redirect by role
    Route::get('/dashboard', [DashboardController::class, 'index'])
         ->name('dashboard');

    // Sync / Pairing
    Route::prefix('sync')->name('sync.')->group(function () {
        Route::get('/', [SyncController::class, 'index'])
             ->name('index');
        Route::post('/pair', [SyncController::class, 'pair'])
             ->name('pair');
        Route::post('/regenerate', [SyncController::class, 'regenerate'])
             ->name('regenerate');
        Route::post('/disconnect', [SyncController::class, 'disconnect'])
             ->name('disconnect');
    });

    // Anggota 3 — Modul Istri
     Route::middleware('role:istri')->group(function () {
     Route::get('/assessment', [\App\Http\Controllers\AssessmentController::class, 'index'])
         ->name('wife.assessment'); 

     Route::post('/assessment', [\App\Http\Controllers\AssessmentController::class, 'store'])
          ->name('wife.assessment.store');
         
     Route::get('/health-summary', [\App\Http\Controllers\AssessmentController::class, 'summary'])
         ->name('wife.health-summary'); 
     
     Route::get('/settings', function() {
          return "Halaman Settings Bunda (Dalam Pengembangan)";
     })->name('wife.settings');
     });

    // Anggota 4 — Modul Suami
    Route::middleware('role:suami')->group(function () {
        Route::get('/missions', fn() => view('husband.missions'))
             ->name('missions.index');
        Route::post('/missions/{mission}/complete', [\App\Http\Controllers\MissionController::class, 'complete'])
             ->name('missions.complete');
    });
     Route::middleware('role:suami')->group(function () {
          Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
          Route::get('/husband/settings', [DashboardController::class, 'settings'])->name('husband.settings');
          Route::put('/husband/settings/update', [DashboardController::class, 'updateSettings'])->name('husband.settings.update');
          Route::post('/husband/settings/disconnect', [DashboardController::class, 'disconnectWife'])->name('husband.settings.disconnect');
     });

    // Anggota 5 — Chat & Klinik
    Route::get('/chat', fn() => view('shared.chat'))
         ->name('chat.index');
    Route::post('/chat/send', [\App\Http\Controllers\ChatController::class, 'send'])
         ->name('chat.send');
    Route::get('/clinics', fn() => view('shared.clinics'))
         ->name('clinics.index');

    // Emergency (dipakai anggota 3 & 5)
    Route::post('/emergency', [\App\Http\Controllers\EmergencyController::class, 'trigger'])
         ->name('emergency.trigger');
});

// ── Broadcasting Auth ──
Route::post('/broadcasting/auth', function () {
    return Auth::check()
        ? Broadcast::auth(request()) // FIX: Gunakan Broadcast Facade
        : abort(403);
})->middleware('auth');

Route::get('/test-api', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'Backend Satuhati berjalan lancar!',
        'data' => [
            'nama_projek' => 'Satuhati',
            'framework' => 'Laravel'
        ]
    ]);
});

// Shortcut Logout (Sesuai kode awal)
Route::post('/logout', function () {
     \Illuminate\Support\Facades\Auth::logout();
     return redirect('/');
 })->name('logout');