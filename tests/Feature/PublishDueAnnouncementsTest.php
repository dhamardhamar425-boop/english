<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Announcement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * Uji perintah penerbitan otomatis pengumuman yang jadwalnya sudah tiba.
 */
final class PublishDueAnnouncementsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::parse('2026-11-15 13:30:00', config('app.timezone')));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    /**
     * Pengumuman yang jadwalnya sudah lewat ikut diterbitkan.
     */
    public function test_publishes_announcements_whose_schedule_has_passed(): void
    {
        $due = Announcement::factory()->create([
            'announcement_date' => '2026-11-15',
            'announcement_time' => '13:00:00',
            'is_published' => false,
            'published_at' => null,
        ]);

        $this->assertDatabaseHas('announcements', ['id' => $due->id, 'is_published' => false]);

        $this->artisan('announcements:publish')
            ->expectsOutputToContain('1 pengumuman diterbitkan')
            ->assertSuccessful();

        $this->assertDatabaseHas('announcements', [
            'id' => $due->id,
            'is_published' => true,
        ]);

        $this->assertNotNull($due->refresh()->published_at);
    }

    /**
     * Pengumuman mendatang tidak disentuh oleh perintah.
     */
    public function test_does_not_publish_upcoming_announcements(): void
    {
        $upcoming = Announcement::factory()->create([
            'announcement_date' => '2026-11-20',
            'announcement_time' => '13:00:00',
            'is_published' => false,
            'published_at' => null,
        ]);

        $this->artisan('announcements:publish')
            ->expectsOutputToContain('Tidak ada pengumuman')
            ->assertSuccessful();

        $this->assertDatabaseHas('announcements', [
            'id' => $upcoming->id,
            'is_published' => false,
        ]);

        $this->assertNull($upcoming->refresh()->published_at);
    }

    /**
     * Pengumuman di hari yang sama tetap menunggu sampai jamnya tiba.
     */
    public function test_does_not_publish_same_day_before_scheduled_time(): void
    {
        $laterToday = Announcement::factory()->create([
            'announcement_date' => '2026-11-15',
            'announcement_time' => '15:00:00',
            'is_published' => false,
            'published_at' => null,
        ]);

        $this->artisan('announcements:publish')->assertSuccessful();

        $this->assertDatabaseHas('announcements', [
            'id' => $laterToday->id,
            'is_published' => false,
        ]);
    }

    /**
     * Perintah aman dijalankan berulang tanpa efek samping (idempoten).
     */
    public function test_command_is_idempotent(): void
    {
        Announcement::factory()->create([
            'announcement_date' => '2026-11-14',
            'announcement_time' => '09:00:00',
            'is_published' => false,
            'published_at' => null,
        ]);

        $this->artisan('announcements:publish')->assertSuccessful();
        $this->artisan('announcements:publish')
            ->expectsOutputToContain('Tidak ada pengumuman')
            ->assertSuccessful();

        $this->assertSame(1, Announcement::where('is_published', true)->count());
    }
}
