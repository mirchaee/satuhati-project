<?php
// app/Http/Controllers/SyncController.php

namespace App\Http\Controllers;

use App\Models\SyncData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SyncController extends Controller
{
    // Halaman Sync — tampilkan kode (istri) atau form input (suami)
    public function index()
    {
        $user = Auth::user();
        $sync = $user->syncRecord;

        return view('shared.sync', [
            'user'        => $user,
            'sync'        => $sync,
            'isPaired'    => $user->isPaired(),
            'partner'     => $user->getPairedPartner(),
            // Kode hanya tampil untuk istri
            'pairingCode' => $user->role === 'istri'
                                ? $sync?->pairing_code
                                : null,
        ]);
    }

    // ════════════════════════════════════════
    //  PAIR — Suami input kode dari istri
    // ════════════════════════════════════════

    public function pair(Request $request)
    {
        $request->validate([
            'pairing_code' => 'required|string|max:10',
        ]);

        $user = Auth::user();

        // Guard: hanya suami yang bisa input kode
        if ($user->role !== 'suami') {
            return back()->withErrors([
                'pairing_code' => 'Hanya suami yang bisa memasukkan kode pasangan.'
            ]);
        }

        // Guard: suami yang sudah paired tidak bisa pair lagi
        if ($user->isPaired()) {
            return back()->withErrors([
                'pairing_code' => 'Kamu sudah terhubung dengan pasangan.'
            ]);
        }

        // Cari kode yang valid:
        // - kode harus ada
        // - belum ada suami (husband_id null)
        // - status masih false (pending)
        $sync = SyncData::where('pairing_code', strtoupper($request->pairing_code))
                        ->whereNull('husband_id')
                        ->where('status', false)
                        ->first();

        if (!$sync) {
            return back()->withErrors([
                'pairing_code' => 'Kode tidak valid, sudah digunakan, atau tidak ditemukan.'
            ]);
        }

        // Pastikan tidak pair dengan diri sendiri
        if ($sync->wife_id === $user->id) {
            return back()->withErrors([
                'pairing_code' => 'Kamu tidak bisa terhubung dengan akunmu sendiri.'
            ]);
        }

        // ⭐ UPDATE — hubungkan suami ke istri
        $sync->update([
            'husband_id' => $user->id,
            'status'     => true,
            'paired_at'  => now(),
        ]);

        return redirect()
            ->route('dashboard')
            ->with('success', '✅ Berhasil terhubung! Selamat mendampingi perjalanan kehamilan.');
    }

    // ════════════════════════════════════════
    //  REGENERATE — Istri buat kode baru
    // ════════════════════════════════════════

    public function regenerate()
    {
        $user = Auth::user();

        if ($user->role !== 'istri') {
            abort(403);
        }

        $sync = $user->syncRecord;

        if (!$sync) {
            // Buat baru jika belum ada (edge case)
            SyncData::create([
                'wife_id'      => $user->id,
                'pairing_code' => SyncData::generateCode(),
                'status'       => false,
            ]);
        } else {
            // Reset koneksi dan buat kode baru
            $sync->update([
                'pairing_code' => SyncData::generateCode(),
                'husband_id'   => null,
                'status'       => false,
                'paired_at'    => null,
            ]);
        }

        return back()->with('success', 'Kode baru berhasil dibuat.');
    }

    // ════════════════════════════════════════
    //  DISCONNECT — Putus koneksi pasangan
    // ════════════════════════════════════════

    public function disconnect(Request $request)
    {
        $request->validate([
            'confirm' => 'required|in:PUTUS', // Konfirmasi teks agar tidak tidak sengaja
        ]);

        $user = Auth::user();
        $sync = $user->syncRecord;

        if (!$sync || !$sync->status) {
            return back()->withErrors(['error' => 'Tidak ada koneksi aktif untuk diputus.']);
        }

        $sync->update([
            'husband_id' => null,
            'status'     => false,
            'paired_at'  => null,
        ]);

        return redirect()
            ->route('sync.index')
            ->with('info', 'Koneksi dengan pasangan telah diputus.');
    }
}