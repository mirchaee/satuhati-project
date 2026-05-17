<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\SyncData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_istri_dapat_register(): void
    {
        $response = $this->post('/register', [
            'name'                  => 'Test Bunda',
            'email'                 => 'bunda@test.com',
            'password'              => 'password',
            'password_confirmation' => 'password',
            'role'                  => 'istri',
            'pregnancy_week'        => 20,
            'hpht'                  => now()->subWeeks(20)->toDateString(),
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertDatabaseHas('users', [
            'email' => 'bunda@test.com',
            'role'  => 'istri',
        ]);

        $user = User::where('email', 'bunda@test.com')->first();
        $this->assertNotNull($user->syncRecord);
        $this->assertNotNull($user->syncRecord->pairing_code);
    }

    public function test_suami_dapat_register(): void
    {
        $response = $this->post('/register', [
            'name'                  => 'Test Ayah',
            'email'                 => 'ayah@test.com',
            'password'              => 'password',
            'password_confirmation' => 'password',
            'role'                  => 'suami',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertDatabaseHas('users', [
            'email' => 'ayah@test.com',
            'role'  => 'suami',
        ]);
    }

    public function test_user_dapat_login(): void
    {
        // Buat user langsung tanpa factory
        User::create([
            'name'           => 'Test User',
            'email'          => 'test@test.com',
            'password'       => bcrypt('password'),
            'role'           => 'istri',          // ← wajib ada
            'pregnancy_week' => 20,
            'hpht'           => now()->subWeeks(20)->toDateString(),
        ]);

        $response = $this->post('/login', [
            'email'    => 'test@test.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }

    public function test_login_gagal_jika_password_salah(): void
    {
        User::create([
            'name'           => 'Test User',
            'email'          => 'test@test.com',
            'password'       => bcrypt('password'),
            'role'           => 'istri',          // ← wajib ada
            'pregnancy_week' => 20,
            'hpht'           => now()->subWeeks(20)->toDateString(),
        ]);

        $response = $this->post('/login', [
            'email'    => 'test@test.com',
            'password' => 'salah123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_dashboard_tidak_bisa_diakses_tanpa_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_user_dapat_logout(): void
    {
        $user = User::create([
            'name'           => 'Test User',
            'email'          => 'test@test.com',
            'password'       => bcrypt('password'),
            'role'           => 'istri',          // ← wajib ada
            'pregnancy_week' => 20,
            'hpht'           => now()->subWeeks(20)->toDateString(),
        ]);

        $this->actingAs($user);

        $response = $this->post('/logout');
        $response->assertRedirect('/login');
        $this->assertGuest();
    }
}