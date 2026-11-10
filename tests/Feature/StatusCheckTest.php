<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\Registration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * Uji alur cek status pendaftaran publik.
 *
 * Kewajiban spesifikasi yang diuji di sini:
 * - Hasil akhir (Diterima/Ditolak) tidak boleh tampil sebelum pengumuman dibuka.
 * - Pembatasan dilakukan di backend, bukan sekadar menyembunyikan di tampilan.
 * - NIK tidak pernah diekspos ke halaman publik.
 */
final class StatusCheckTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    /**
     * Halaman formulir cek status dapat diakses publik.
     */
    public function test_status_check_form_is_accessible(): void
    {
        $this->get(route('status.check'))->assertOk();
    }

    /**
     * Hasil akhir disembunyikan sampai pengumuman resmi dibuka.
     */
    public function test_final_result_is_hidden_before_announcement_opens(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-11-10 10:00:00', config('app.timezone')));

        Announcement::factory()->create([
            'type' => 'result',
            'announcement_date' => '2026-11-15',
            'announcement_time' => '13:00:00',
            'is_published' => true,
        ]);

        $registration = Registration::factory()->diterima()->create();

        $response = $this->followingRedirects()
            ->post(route('status.store'), ['identifier' => $registration->registration_number]);

        $response->assertOk()
            ->assertSee('BELUM DIUMUMKAN', false)
            ->assertDontSee('DITERIMA', false);
    }

    /**
     * Setelah jadwal pengumuman tiba, hasil akhir tampil ke siswa.
     */
    public function test_final_result_is_visible_after_announcement_opens(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-11-15 13:05:00', config('app.timezone')));

        Announcement::factory()->create([
            'type' => 'result',
            'announcement_date' => '2026-11-15',
            'announcement_time' => '13:00:00',
            'is_published' => true,
        ]);

        $registration = Registration::factory()->diterima()->create();

        $response = $this->followingRedirects()
            ->post(route('status.store'), ['identifier' => $registration->registration_number]);

        $response->assertOk()->assertSee('DITERIMA', false);
    }

    /**
     * NIK tidak pernah ditampilkan di halaman hasil publik.
     */
    public function test_nik_is_never_exposed_on_public_result_page(): void
    {
        $registration = Registration::factory()->diterima()->create();

        $response = $this->followingRedirects()
            ->post(route('status.store'), ['identifier' => $registration->registration_number]);

        $response->assertOk()->assertDontSee($registration->nik, false);
    }

    /**
     * Mencari dengan NIS juga menemukan pendaftaran.
     */
    public function test_lookup_by_nis_finds_registration(): void
    {
        $registration = Registration::factory()->create();

        $this->followingRedirects()
            ->post(route('status.store'), ['identifier' => $registration->nis])
            ->assertOk()
            ->assertSee($registration->registration_number, false);
    }

    /**
     * Identifier yang tidak ditemukan menghasilkan pesan galat.
     */
    public function test_unknown_identifier_returns_validation_error(): void
    {
        $this->from(route('status.check'))
            ->post(route('status.store'), ['identifier' => 'TIDAK-ADA'])
            ->assertRedirect(route('status.check'))
            ->assertSessionHasErrors('identifier');
    }

    /**
     * Halaman hasil tampil fullscreen tanpa navbar dan footer publik.
     */
    public function test_result_page_renders_fullscreen_without_public_chrome(): void
    {
        $registration = Registration::factory()->diterima()->create();

        $this->followingRedirects()
            ->post(route('status.store'), ['identifier' => $registration->registration_number])
            ->assertOk()
            ->assertDontSee('Portal Organisasi Siswa', false)
            ->assertDontSee('Tautan Cepat', false);
    }

    /**
     * Halaman hasil tanpa sesi pencarian diarahkan kembali ke formulir.
     */
    public function test_result_page_without_session_redirects_to_form(): void
    {
        $this->get(route('status.result'))->assertRedirect(route('status.check'));
    }
}
