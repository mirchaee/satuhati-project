<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\SyncController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MissionController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\EmergencyController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;

// ── Root ────────────────────────────────────────
Route::get('/', function () {
    return view('landing');
});

// ── Auth (hanya untuk tamu / belum login) ───────
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// ── Logout ──────────────────────────────────────
Route::post('/logout', [AuthController::class, 'logout'])
     ->name('logout')
     ->middleware('auth');

// ── Protected (harus login) ─────────────────────
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── Anggota 3 — Modul Istri ─────────────────────
    Route::middleware('role:istri')->group(function () {
        Route::get('/assessment', [AssessmentController::class, 'index'])->name('wife.assessment'); 
        Route::post('/assessment', [AssessmentController::class, 'store'])->name('wife.assessment.store');
        Route::get('/health-summary', [AssessmentController::class, 'summary'])->name('wife.health-summary'); 
        
        // Rute Pengaturan & Putus Hubungan Istri milikmu yang sudah fix
        Route::get('/settings', [DashboardController::class, 'settings'])->name('wife.settings');
        Route::put('/settings/update', [DashboardController::class, 'updateSettings'])->name('wife.settings.update');
        Route::delete('/settings/disconnect', [DashboardController::class, 'disconnectHusband'])->name('wife.disconnect');
    });

    // ── Anggota 4 — Modul Suami ─────────────────────
    Route::middleware('role:suami')->group(function () {
        Route::prefix('sync')->name('sync.')->group(function () {
            Route::get('/', [SyncController::class, 'index'])->name('index');
            Route::post('/pair', [SyncController::class, 'pair'])->name('pair');
            Route::post('/regenerate', [SyncController::class, 'regenerate'])->name('regenerate');
            Route::post('/disconnect', [SyncController::class, 'disconnect'])->name('disconnect');
        });
        
        Route::get('/missions', [DashboardController::class, 'allMissions'])->name('missions.index');
        Route::post('/missions/{id}/complete', [MissionController::class, 'complete'])->name('missions.complete');
        
        Route::get('/husband/settings', [DashboardController::class, 'settings'])->name('husband.settings');
        Route::put('/husband/settings/update', [DashboardController::class, 'updateSettings'])->name('husband.settings.update');
        Route::post('/husband/settings/disconnect', [DashboardController::class, 'disconnectWife'])->name('husband.settings.disconnect');
    });

    // ── Rute Bersama (Shared) ────────────────────────
    Route::get('/chat', fn() => view('shared.chat'))->name('chat.index');
    Route::post('/chat/send', [ChatController::class, 'send'])->name('chat.send');
    Route::get('/clinics', fn() => view('shared.clinics'))->name('clinics.index');

    Route::post('/emergency', [EmergencyController::class, 'trigger'])->name('emergency.trigger');
});

// ── Broadcasting Auth ──
Route::post('/broadcasting/auth', function () {
    return Auth::check() ? Broadcast::auth(request()) : abort(403);
})->middleware('auth');

// ── API Test ──
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