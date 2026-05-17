<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\SyncData;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ── Istri (sudah paired) ─────────────────
        $istri = User::create([
            'name'           => 'Sarah Bunda',
            'email'          => 'istri@test.com',
            'password'       => Hash::make('password'),
            'role'           => 'istri',
            'pregnancy_week' => 24,
            'hpht'           => now()->subWeeks(24)->toDateString(),
            'phone'          => '08123456789',
        ]);

        // ── Suami (sudah paired) ─────────────────
        $suami = User::create([
            'name'     => 'Budi Ayah',
            'email'    => 'suami@test.com',
            'password' => Hash::make('password'),
            'role'     => 'suami',
            'phone'    => '08987654321',
        ]);

        // ── Sync record (sudah terhubung) ────────
        SyncData::create([
            'wife_id'      => $istri->id,
            'husband_id'   => $suami->id,
            'pairing_code' => 'SH-AB12',
            'status'       => true,
            'paired_at'    => now(),
        ]);

        // ── Istri belum paired (untuk testing alur pairing) ──
        $istri2 = User::create([
            'name'           => 'Dewi Calon Bunda',
            'email'          => 'istri2@test.com',
            'password'       => Hash::make('password'),
            'role'           => 'istri',
            'pregnancy_week' => 12,
            'hpht'           => now()->subWeeks(12)->toDateString(),
        ]);

        SyncData::create([
            'wife_id'      => $istri2->id,
            'husband_id'   => null,
            'pairing_code' => 'SH-CD34',
            'status'       => false,
        ]);

        // ── Suami belum paired ───────────────────
        User::create([
            'name'     => 'Rudi Calon Ayah',
            'email'    => 'suami2@test.com',
            'password' => Hash::make('password'),
            'role'     => 'suami',
        ]);
    }
}