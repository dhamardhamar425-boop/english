<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Tamu diarahkan ke halaman login ketika membuka dashboard.
     */
    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
    }

    /**
     * Halaman login tampil dan berisi formulir.
     */
    public function test_login_page_displays(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Masuk ke Dashboard Admin')
            ->assertSee('email', false);
    }

    /**
     * Pengguna terautentikasi tidak ditampilkan halaman login lagi.
     */
    public function test_authenticated_user_is_redirected_from_login_page(): void
    {
        $user = User::factory()->superAdmin()->create();

        $this->actingAs($user)->get(route('login'))->assertRedirect(route('admin.dashboard'));
    }

    /**
     * Admin berhasil login dengan kredensial yang benar.
     */
    public function test_admin_can_login_with_valid_credentials(): void
    {
        $organization = Organization::factory()->create();
        $admin = User::factory()->orgAdmin($organization)->create(['email' => 'osis@smk.sch.id']);

        $response = $this->from(route('login'))->post(route('login.attempt'), [
            'email' => 'osis@smk.sch.id',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
        $this->assertDatabaseHas('audit_logs', ['action' => 'auth.login']);
    }

    /**
     * Kredensial salah ditolak dengan pesan validasi.
     */
    public function test_login_fails_with_invalid_credentials(): void
    {
        User::factory()->superAdmin()->create(['email' => 'admin@smk.sch.id']);

        $response = $this->from(route('login'))->post(route('login.attempt'), [
            'email' => 'admin@smk.sch.id',
            'password' => 'salah-password',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /**
     * Percobaan login dibatasi untuk mencegah brute force.
     */
    public function test_login_attempts_are_rate_limited(): void
    {
        User::factory()->superAdmin()->create(['email' => 'admin@smk.sch.id']);

        foreach (range(1, 5) as $attempt) {
            $this->post(route('login.attempt'), [
                'email' => 'admin@smk.sch.id',
                'password' => 'password-salah',
            ]);
        }

        $this->post(route('login.attempt'), [
            'email' => 'admin@smk.sch.id',
            'password' => 'password-salah',
        ])->assertStatus(429);
    }

    /**
     * Admin dapat logout dan sesinya dihancurkan.
     */
    public function test_admin_can_logout(): void
    {
        $user = User::factory()->superAdmin()->create();

        $response = $this->actingAs($user)->post(route('logout'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
        $this->assertDatabaseHas('audit_logs', ['action' => 'auth.logout']);
    }

    /**
     * Password disimpan dalam bentuk ter-hash, bukan teks biasa.
     */
    public function test_password_is_hashed_in_database(): void
    {
        $user = User::factory()->superAdmin()->create(['password' => 'password']);

        $this->assertNotSame('password', $user->getRawOriginal('password'));
        $this->assertTrue(Hash::check('password', $user->password));
    }
}
