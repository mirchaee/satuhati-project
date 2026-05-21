<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\SyncController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

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

    // ── Fitur lain (untuk dikerjakan anggota lain) ──
    // Routes di bawah ini kamu buat sekarang agar
    // anggota lain tinggal bikin controllernya

    // Anggota 3 — Modul Istri
    Route::middleware('role:istri')->group(function () {
        Route::get('/assessment', fn() => view('wife.assessment'))
             ->name('assessment.index');
        Route::post('/assessment', [\App\Http\Controllers\AssessmentController::class, 'store'])
             ->name('assessment.store');
        Route::get('/health-summary', [\App\Http\Controllers\AssessmentController::class, 'summary'])
             ->name('assessment.summary');
    });

    // Anggota 4 — Modul Suami
    Route::middleware('role:suami')->group(function () {
        Route::get('/missions', fn() => view('husband.missions'))
             ->name('missions.index');
        Route::post('/missions/{mission}/complete', [\App\Http\Controllers\MissionController::class, 'complete'])
             ->name('missions.complete');
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

// ── Broadcasting Auth (Pusher private channel) ──
Route::post('/broadcasting/auth', function () {
    return \Illuminate\Support\Facades\Auth::check()
        ? broadcast()->auth(request())
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




//ini nanti ubah aja nggapapa, cuman buat sementara biar gampang logoutnya, soalnya kalo pake post kan harus bikin form segala, jadi ini aja dulu

Route::get('/logout', function () {

    Auth::logout();

    return redirect('/');

});