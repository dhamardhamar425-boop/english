<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\AnnouncementType;
use App\Models\Announcement;
use App\Models\Organization;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * Uji pengaturan pengumuman hasil seleksi serentak untuk semua organisasi.
 */
final class AdminResultAnnouncementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::parse('2026-11-10 08:00:00', config('app.timezone')));

        Setting::set(Setting::REGISTRATION_IS_OPEN, '1');
        Setting::set(Setting::REGISTRATION_START, '2026-10-01');
        Setting::set(Setting::REGISTRATION_END, '2026-12-31');
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    /**
     * Super Admin dapat menjadwalkan pengumuman hasil seleksi serentak.
     */
    public function test_super_admin_can_schedule_result_announcement(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($superAdmin)
            ->post(route('admin.settings.result-announcement.update'), [
                'announcement_date' => '2026-11-15',
                'announcement_time' => '13:00',
                'description' => 'Hasil seleksi diumumkan serentak.',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $announcement = Announcement::query()->where('type', AnnouncementType::Result)->firstOrFail();

        $this->assertSame('2026-11-15', $announcement->announcement_date->toDateString());
        $this->assertSame('13:00:00', $announcement->announcement_time);
        $this->assertSame('Hasil seleksi diumumkan serentak.', $announcement->description);

        $this->assertFalse(Announcement::resultsAreVisible());
        $this->assertTrue(Setting::isRegistrationOpen());
    }

    /**
     * Jadwal yang disimpan memperbarui pengumuman hasil seleksi yang sudah ada
     * (tetap satu pengumuman global, tidak menumpuk).
     */
    public function test_scheduling_updates_the_existing_result_announcement(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $existing = Announcement::factory()->result()->create([
            'announcement_date' => '2026-11-15',
            'announcement_time' => '13:00:00',
        ]);

        $this->actingAs($superAdmin)
            ->post(route('admin.settings.result-announcement.update'), [
                'announcement_date' => '2026-11-20',
                'announcement_time' => '09:30',
            ])
            ->assertSessionHasNoErrors();

        $this->assertSame(1, Announcement::query()->where('type', AnnouncementType::Result)->count());

        $existing->refresh();

        $this->assertSame('2026-11-20', $existing->announcement_date->toDateString());
        $this->assertSame('09:30:00', $existing->announcement_time);
    }

    /**
     * Tombol "Buka Sekarang" langsung menampilkan hasil ke siswa dan
     * menutup pendaftaran untuk semua organisasi.
     */
    public function test_open_now_shows_results_immediately_and_closes_registration(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($superAdmin)
            ->post(route('admin.settings.result-announcement.open'))
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $announcement = Announcement::query()->where('type', AnnouncementType::Result)->firstOrFail();

        $this->assertSame('2026-11-10', $announcement->announcement_date->toDateString());
        $this->assertSame('08:00:00', $announcement->announcement_time);
        $this->assertTrue($announcement->is_published);

        $this->assertTrue(Announcement::resultsAreVisible());
        $this->assertFalse(Setting::isRegistrationOpen());
        $this->assertSame('ditutup', Setting::registrationState());
    }

    /**
     * Halaman pengaturan menampilkan panel pengumuman hasil seleksi.
     */
    public function test_settings_page_shows_result_announcement_panel(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($superAdmin)
            ->get(route('admin.settings.index'))
            ->assertOk()
            ->assertSee('Pengumuman Hasil Seleksi', false)
            ->assertSee('Buka Pengumuman Sekarang', false);
    }

    /**
     * Admin organisasi tidak boleh mengatur pengumuman hasil seleksi.
     */
    public function test_org_admin_cannot_manage_result_announcement(): void
    {
        $organization = Organization::factory()->create();
        $orgAdmin = User::factory()->orgAdmin($organization)->create();

        $this->actingAs($orgAdmin)
            ->post(route('admin.settings.result-announcement.open'))
            ->assertForbidden();

        $this->assertSame(0, Announcement::query()->where('type', AnnouncementType::Result)->count());
    }
}
