<?php

namespace Tests\Feature;

use App\Enums\AnnouncementState;
use App\Enums\AnnouncementType;
use App\Enums\FinalResult;
use App\Models\Announcement;
use App\Models\Organization;
use App\Models\Registration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrganizationModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Relasi organisasi ke pendaftar, kegiatan, galeri, dan admin.
     */
    public function test_organization_relationships(): void
    {
        $organization = Organization::factory()->create();

        Registration::factory()->count(2)->create(['organization_id' => $organization->id]);
        $organization->organizationActivities()->create(['title' => 'Latihan Pagi']);

        $this->assertCount(2, $organization->registrations);
        $this->assertSame(1, $organization->organizationActivities()->count());
        $this->assertSame(0, $organization->galleries()->count());
    }

    /**
     * Inisial organisasi dipakai sebagai fallback logo.
     */
    public function test_organization_initials_fallback(): void
    {
        $osis = Organization::factory()->create(['name' => 'OSIS']);
        $ipnu = Organization::factory()->create(['name' => 'IPNU-IPPNU']);

        $this->assertSame('OS', $osis->initials());
        $this->assertSame('II', $ipnu->initials());
        $this->assertNull($osis->logoUrl());
    }

    /**
     * Hasil akhir tidak boleh terlihat sebelum pengumuman resmi dibuka.
     */
    public function test_final_result_hidden_before_announcement(): void
    {
        Announcement::factory()->result()->create([
            'announcement_date' => now()->addDays(3)->toDateString(),
            'announcement_time' => '13:00:00',
            'is_published' => false,
        ]);

        $registration = Registration::factory()->diterima()->create();

        $this->assertSame(AnnouncementState::NotOpen, Announcement::resultState());
        $this->assertFalse(Announcement::resultsAreVisible());
        $this->assertNull($registration->visibleFinalResult());
        $this->assertSame(FinalResult::Diterima, $registration->final_result);
    }

    /**
     * Hasil akhir terlihat setelah jadwal pengumuman terbuka.
     */
    public function test_final_result_visible_after_announcement(): void
    {
        Announcement::factory()->result()->create([
            'announcement_date' => now()->subDay()->toDateString(),
            'announcement_time' => '13:00:00',
            'is_published' => true,
            'published_at' => now()->subDay(),
        ]);

        $registration = Registration::factory()->diterima()->create();

        $this->assertSame(AnnouncementState::Opened, Announcement::resultState());
        $this->assertTrue(Announcement::resultsAreVisible());
        $this->assertSame(FinalResult::Diterima, $registration->visibleFinalResult());
    }

    /**
     * Indikator pengumuman: belum dibuka, sedang berlangsung, sudah dibuka.
     */
    public function test_announcement_state_transitions(): void
    {
        $announcement = Announcement::factory()->result()->create([
            'announcement_date' => now()->toDateString(),
            'announcement_time' => now()->addHour()->format('H:i:s'),
        ]);

        $this->assertSame(AnnouncementState::NotOpen, $announcement->state());

        $announcement->update(['announcement_time' => now()->subHour()->format('H:i:s')]);

        $this->assertSame(AnnouncementState::Ongoing, $announcement->fresh()->state());

        $announcement->update([
            'announcement_date' => now()->subDays(2)->toDateString(),
            'announcement_time' => '13:00:00',
        ]);

        $this->assertSame(AnnouncementState::Opened, $announcement->fresh()->state());
    }

    /**
     * Tanpa jadwal pengumuman hasil, hasil seleksi tidak pernah tampil.
     */
    public function test_no_result_announcement_means_results_hidden(): void
    {
        $registration = Registration::factory()->ditolak()->create();

        $this->assertSame(AnnouncementState::NotScheduled, Announcement::resultState());
        $this->assertNull($registration->visibleFinalResult());
    }

    /**
     * Pengumuman hasil yang dianalisis adalah jadwal terbaru.
     */
    public function test_latest_result_announcement_is_active(): void
    {
        Announcement::factory()->result()->create([
            'announcement_date' => now()->subYear()->toDateString(),
            'announcement_time' => '13:00:00',
        ]);

        $latest = Announcement::factory()->result()->create([
            'announcement_date' => now()->addDays(10)->toDateString(),
            'announcement_time' => '13:00:00',
        ]);

        $this->assertTrue(
            Announcement::activeResultAnnouncement()?->is($latest),
        );
        $this->assertSame(AnnouncementType::Result, $latest->type);
    }
}
