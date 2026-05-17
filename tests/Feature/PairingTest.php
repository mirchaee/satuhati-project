<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\SyncData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PairingTest extends TestCase
{
    use RefreshDatabase;

    public function test_suami_dapat_pair_dengan_kode_valid(): void
    {
        // Buat istri
        $istri = User::create([
            'name'           => 'Test Istri',
            'email'          => 'istri@test.com',
            'password'       => bcrypt('password'),
            'role'           => 'istri',
            'pregnancy_week' => 20,
            'hpht'           => now()->subWeeks(20)->toDateString(),
        ]);

        SyncData::create([
            'wife_id'      => $istri->id,
            'pairing_code' => 'SH-TEST',
            'status'       => false,
        ]);

        // Buat suami dan login
        $suami = User::create([
            'name'     => 'Test Suami',
            'email'    => 'suami@test.com',
            'password' => bcrypt('password'),
            'role'     => 'suami',
        ]);

        $this->actingAs($suami);

        $response = $this->post('/sync/pair', [
            'pairing_code' => 'SH-TEST',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertDatabaseHas('sync_data', [
            'pairing_code' => 'SH-TEST',
            'husband_id'   => $suami->id,
            'status'       => true,
        ]);
    }

    public function test_pairing_gagal_jika_kode_salah(): void
    {
        $suami = User::create([
            'name'     => 'Test Suami',
            'email'    => 'suami@test.com',
            'password' => bcrypt('password'),
            'role'     => 'suami',
        ]);

        $this->actingAs($suami);

        $response = $this->post('/sync/pair', [
            'pairing_code' => 'SALAH-123',
        ]);

        $response->assertSessionHasErrors('pairing_code');
    }

    public function test_istri_tidak_bisa_input_kode(): void
    {
        $istri = User::create([
            'name'           => 'Test Istri',
            'email'          => 'istri@test.com',
            'password'       => bcrypt('password'),
            'role'           => 'istri',
            'pregnancy_week' => 20,
            'hpht'           => now()->subWeeks(20)->toDateString(),
        ]);

        SyncData::create([
            'wife_id'      => $istri->id,
            'pairing_code' => 'SH-TEST',
            'status'       => false,
        ]);

        $this->actingAs($istri);

        $response = $this->post('/sync/pair', [
            'pairing_code' => 'SH-TEST',
        ]);

        $response->assertSessionHasErrors('pairing_code');
    }

    public function test_pairing_code_selalu_unik(): void
    {
        $kode1 = SyncData::generateCode();
        $kode2 = SyncData::generateCode();
        $kode3 = SyncData::generateCode();

        $this->assertStringStartsWith('SH-', $kode1);
        $this->assertStringStartsWith('SH-', $kode2);
        $this->assertNotEquals($kode1, $kode2);
        $this->assertNotEquals($kode2, $kode3);
    }
}