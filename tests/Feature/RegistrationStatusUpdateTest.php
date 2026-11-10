<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RegistrationStatus;
use App\Models\Announcement;
use App\Models\Organization;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Uji alur perubahan status seleksi oleh admin:
 * - transisi status wajar + riwayat + audit log,
 * - pembatasan organisasi (admin organisasi hanya miliknya),
 * - hasil akhir hanya Super Admin setelah pengumuman dibuka.
 */
final class RegistrationStatusUpdateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Admin organisasi dapat mengubah status pendaftar organisasinya,
     * dan perubahan tercatat sebagai riwayat + audit log.
     */
    public function test_org_admin_can_update_status_and_it_is_recorded(): void
    {
        $organization = Organization::factory()->create();
        $admin = User::factory()->orgAdmin($organization)->create();
        $registration = Registration::factory()->create([
            'organization_id' => $organization->id,
            'status' => RegistrationStatus::Menunggu,
        ]);

        $response = $this->actingAs($admin)->patch(
            route('admin.registrations.updateStatus', $registration),
            ['status' => 'seleksi', 'notes' => 'Lolos administrasi.'],
        );

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('registrations', [
            'id' => $registration->id,
            'status' => RegistrationStatus::Seleksi->value,
        ]);

        $this->assertDatabaseHas('registration_status_histories', [
            'registration_id' => $registration->id,
            'admin_id' => $admin->id,
            'old_status' => RegistrationStatus::Menunggu->value,
            'new_status' => RegistrationStatus::Seleksi->value,
            'notes' => 'Lolos administrasi.',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $admin->id,
            'action' => 'ubah_status_pendaftar',
        ]);
    }

    /**
     * Memilih status hasil akhir menyetel kolom final_result secara otomatis.
     */
    public function test_selecting_final_status_sets_final_result(): void
    {
        $organization = Organization::factory()->create();
        $admin = User::factory()->orgAdmin($organization)->create();
        $registration = Registration::factory()->create([
            'organization_id' => $organization->id,
            'status' => RegistrationStatus::Seleksi,
            'final_result' => null,
        ]);

        $this->actingAs($admin)->patch(
            route('admin.registrations.updateStatus', $registration),
            ['status' => 'diterima'],
        );

        $this->assertDatabaseHas('registrations', [
            'id' => $registration->id,
            'status' => RegistrationStatus::Diterima->value,
            'final_result' => 'diterima',
        ]);
    }

    /**
     * Admin organisasi TIDAK dapat mengubah pendaftar organisasi lain (403).
     */
    public function test_org_admin_cannot_update_other_organization_registration(): void
    {
        $osis = Organization::factory()->create();
        $pmr = Organization::factory()->create();
        $adminOsis = User::factory()->orgAdmin($osis)->create();
        $registrationPmr = Registration::factory()->create(['organization_id' => $pmr->id]);

        $this->actingAs($adminOsis)
            ->patch(route('admin.registrations.updateStatus', $registrationPmr), ['status' => 'seleksi'])
            ->assertForbidden();
    }

    /**
     * Setelah pengumuman hasil dibuka, admin organisasi TIDAK dapat
     * mengubah hasil akhir; hanya Super Admin yang boleh.
     */
    public function test_after_announcement_only_super_admin_can_change_final_result(): void
    {
        Announcement::factory()->create([
            'type' => 'result',
            'announcement_date' => now()->subDay()->toDateString(),
            'announcement_time' => '00:00:00',
            'is_published' => true,
        ]);

        $organization = Organization::factory()->create();
        $orgAdmin = User::factory()->orgAdmin($organization)->create();
        $registration = Registration::factory()->create([
            'organization_id' => $organization->id,
            'status' => RegistrationStatus::Diterima,
            'final_result' => 'diterima',
        ]);

        $response = $this->actingAs($orgAdmin)->patch(
            route('admin.registrations.updateStatus', $registration),
            ['status' => 'ditolak'],
        );

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('registrations', [
            'id' => $registration->id,
            'final_result' => 'diterima',
        ]);
    }

    /**
     * Super Admin tetap dapat mengubah hasil akhir setelah pengumuman dibuka.
     */
    public function test_super_admin_can_change_final_result_after_announcement(): void
    {
        Announcement::factory()->create([
            'type' => 'result',
            'announcement_date' => now()->subDay()->toDateString(),
            'announcement_time' => '00:00:00',
            'is_published' => true,
        ]);

        $organization = Organization::factory()->create();
        $superAdmin = User::factory()->superAdmin()->create();
        $registration = Registration::factory()->create([
            'organization_id' => $organization->id,
            'status' => RegistrationStatus::Diterima,
            'final_result' => 'diterima',
        ]);

        $this->actingAs($superAdmin)
            ->patch(route('admin.registrations.updateStatus', $registration), ['status' => 'ditolak'])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('registrations', [
            'id' => $registration->id,
            'final_result' => 'ditolak',
        ]);
    }

    /**
     * Tamu tidak dapat mengubah status (dialihkan ke login).
     */
    public function test_guest_cannot_update_status(): void
    {
        $organization = Organization::factory()->create();
        $registration = Registration::factory()->create(['organization_id' => $organization->id]);

        $this->patch(route('admin.registrations.updateStatus', $registration), ['status' => 'seleksi'])
            ->assertRedirect(route('login'));
    }
}
