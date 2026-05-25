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
            
            'pairingCode' => $user->role === 'istri' ? $user->pairing_code : null,
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

        if ($user->role !== 'suami') {
            return back()->withErrors([
                'pairing_code' => 'Hanya suami yang bisa memasukkan kode pasangan.'
            ]);
        }

        if ($user->isPaired()) {
            return back()->withErrors([
                'pairing_code' => 'Kamu sudah terhubung dengan pasangan.'
            ]);
        }

        $wifeUser = \App\Models\User::where('pairing_code', strtoupper($request->pairing_code))
                                    ->where('role', 'istri')
                                    ->first();
        if (!$wifeUser) {
            return back()->withErrors([
                'pairing_code' => 'Kode tidak valid atau tidak ditemukan.'
            ]);
        }
        $sync = \Illuminate\Support\Facades\DB::table('sync_data')
            ->where('wife_id', $wifeUser->id)
            ->first();

        if ($sync && $sync->status) {
            return back()->withErrors([
                'pairing_code' => 'Kode ini sudah digunakan oleh akun lain.'
            ]);
        }
        if ($sync) {
            \Illuminate\Support\Facades\DB::table('sync_data')
                ->where('id', $sync->id)
                ->update([
                    'husband_id' => $user->id,
                    'status'     => true,
                    'paired_at'  => now(),
                    'updated_at' => now(),
                ]);
        } else {
            \Illuminate\Support\Facades\DB::table('sync_data')->insert([
                'wife_id'      => $wifeUser->id,
                'husband_id'   => $user->id,
                'pairing_code' => $wifeUser->pairing_code,
                'status'       => true,
                'paired_at'    => now(),
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }

        return redirect()->route('dashboard')
            ->with('success', 'Berhasil terhubung! Selamat mendampingi perjalanan kehamilan.');
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