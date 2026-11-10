<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\AnnouncementType;
use App\Models\Announcement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * Uji pengelolaan pengumuman oleh Super Admin.
 */
final class AdminAnnouncementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::parse('2026-11-10 08:00:00', config('app.timezone')));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    /**
     * Super Admin dapat mengundur jadwal pengumuman hasil seleksi.
     */
    public function test_super_admin_can_postpone_result_announcement(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $announcement = Announcement::factory()->result()->create([
            'announcement_date' => '2026-11-10',
            'announcement_time' => '07:00:00',
            'is_published' => true,
        ]);

        $response = $this->actingAs($superAdmin)->put(route('admin.announcements.update', $announcement), [
            'title' => $announcement->title,
            'description' => $announcement->description,
            'type' => 'result',
            'announcement_date' => '2026-11-20',
            'announcement_time' => '13:00',
            'is_published' => '1',
        ]);

        $response->assertRedirect();

        $announcement->refresh();

        $this->assertSame('2026-11-20', $announcement->announcement_date->toDateString());
        $this->assertSame('13:00:00', $announcement->announcement_time);
        $this->assertSame(AnnouncementType::Result, $announcement->type);
    }

    /**
     * Kesalahan validasi form edit masuk ke error bag khusus form edit
     * agar tidak bocor ke form tambah pengumuman.
     */
    public function test_invalid_update_reports_error_in_announcement_update_bag(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $announcement = Announcement::factory()->result()->create([
            'announcement_date' => '2026-11-10',
            'announcement_time' => '07:00:00',
        ]);

        $this->actingAs($superAdmin)
            ->put(route('admin.announcements.update', $announcement), [
                'title' => '',
                'type' => 'result',
                'announcement_date' => '2026-11-20',
                'announcement_time' => '13:00',
            ])
            ->assertSessionHasErrorsIn('announcementUpdate', ['title']);
    }
}
