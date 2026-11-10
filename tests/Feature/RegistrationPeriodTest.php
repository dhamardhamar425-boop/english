<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * Uji status periode pendaftaran, termasuk penutupan otomatis setelah
 * jadwal pengumuman hasil seleksi tiba.
 */
final class RegistrationPeriodTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    /**
     * Atur periode pendaftaran yang sedang terbuka.
     */
    private function openPeriod(): void
    {
        Setting::set(Setting::REGISTRATION_IS_OPEN, '1');
        Setting::set(Setting::REGISTRATION_START, '2026-10-01');
        Setting::set(Setting::REGISTRATION_END, '2026-12-31');
    }

    /**
     * Sebelum jadwal pengumuman, pendaftaran tetap dibuka.
     */
    public function test_registration_is_open_before_result_announcement(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-11-10 08:00:00', config('app.timezone')));
        $this->openPeriod();

        Announcement::factory()->result()->create([
            'announcement_date' => '2026-11-15',
            'announcement_time' => '13:00:00',
        ]);

        $this->assertSame('buka', Setting::registrationState());
        $this->assertTrue(Setting::isRegistrationOpen());
    }

    /**
     * Setelah jadwal pengumuman tiba, pendaftaran otomatis ditutup.
     */
    public function test_registration_closes_after_result_announcement_is_opened(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-11-15 13:05:00', config('app.timezone')));
        $this->openPeriod();

        Announcement::factory()->result()->create([
            'announcement_date' => '2026-11-15',
            'announcement_time' => '13:00:00',
        ]);

        $this->assertSame('ditutup', Setting::registrationState());
        $this->assertFalse(Setting::isRegistrationOpen());
    }

    /**
     * Mengundur jadwal pengumuman membuka kembali pendaftaran
     * selama periode pendaftaran masih berjalan.
     */
    public function test_postponing_result_announcement_reopens_registration(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-11-15 13:05:00', config('app.timezone')));
        $this->openPeriod();

        $announcement = Announcement::factory()->result()->create([
            'announcement_date' => '2026-11-15',
            'announcement_time' => '13:00:00',
        ]);

        $this->assertFalse(Setting::isRegistrationOpen());

        $announcement->update([
            'announcement_date' => '2026-11-20',
            'announcement_time' => '13:00:00',
        ]);

        $this->assertSame('buka', Setting::registrationState());
        $this->assertTrue(Setting::isRegistrationOpen());
    }

    /**
     * Halaman utama menampilkan pesan pendaftaran ditutup setelah pengumuman.
     */
    public function test_home_page_shows_closed_message_after_announcement(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-11-15 13:05:00', config('app.timezone')));
        $this->openPeriod();

        Announcement::factory()->result()->create([
            'announcement_date' => '2026-11-15',
            'announcement_time' => '13:00:00',
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Pendaftaran Organisasi Masa Bakti 2026 Telah Ditutup', false);
    }
}
