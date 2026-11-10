<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RegistrationStatus;
use App\Models\Organization;
use App\Models\Registration;
use App\Models\RegistrationStatusHistory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Smoke test: seluruh halaman publik & dashboard admin harus merender
 * tanpa error (menangkap variabel view yang hilang).
 */
final class PageRenderingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Halaman publik yang dapat diakses tanpa login.
     */
    public function test_public_pages_render_successfully(): void
    {
        $organization = Organization::factory()->create();

        $pages = [
            route('home'),
            route('organizations.index'),
            route('organizations.show', $organization),
            route('registration.flow'),
            route('pages.about'),
            route('announcements.index'),
            route('registrations.create'),
            route('status.check'),
        ];

        foreach ($pages as $page) {
            $this->get($page)->assertOk();
        }
    }

    /**
     * Halaman dashboard admin (Super Admin) merender dengan benar,
     * termasuk halaman detail dengan data pendaftar nyata.
     */
    public function test_super_admin_dashboard_pages_render_successfully(): void
    {
        $organization = Organization::factory()->create();
        $registration = Registration::factory()->create(['organization_id' => $organization->id]);
        RegistrationStatusHistory::create([
            'registration_id' => $registration->id,
            'admin_id' => null,
            'old_status' => null,
            'new_status' => RegistrationStatus::Seleksi,
            'notes' => 'Masuk tahap seleksi.',
        ]);
        $superAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($superAdmin);

        foreach ($this->superAdminPages() as $page) {
            $this->get($page)->assertOk();
        }

        $this->get(route('admin.registrations.show', $registration))->assertOk();
        $this->get(route('admin.organizations.show', $organization))->assertOk();
    }

    /**
     * Halaman dashboard admin organisasi merender dengan benar.
     */
    public function test_org_admin_dashboard_pages_render_successfully(): void
    {
        $organization = Organization::factory()->create();
        $registration = Registration::factory()->create(['organization_id' => $organization->id]);
        $orgAdmin = User::factory()->orgAdmin($organization)->create();

        $this->actingAs($orgAdmin);

        $pages = [
            route('admin.dashboard'),
            route('admin.registrations.index'),
            route('admin.registrations.pending'),
            route('admin.organizations.show', $organization),
            route('admin.registrations.show', $registration),
        ];

        foreach ($pages as $page) {
            $this->get($page)->assertOk();
        }
    }

    /**
     * Admin organisasi tidak dapat melihat pendaftar organisasi lain (403).
     */
    public function test_org_admin_cannot_view_other_organization_registration(): void
    {
        $osis = Organization::factory()->create();
        $pmr = Organization::factory()->create();
        $registrationPmr = Registration::factory()->create(['organization_id' => $pmr->id]);
        $orgAdmin = User::factory()->orgAdmin($osis)->create();

        $this->actingAs($orgAdmin)
            ->get(route('admin.registrations.show', $registrationPmr))
            ->assertForbidden();
    }

    /**
     * Admin organisasi tidak boleh mengakses halaman khusus Super Admin.
     */
    public function test_org_admin_cannot_access_super_admin_pages(): void
    {
        $organization = Organization::factory()->create();
        $orgAdmin = User::factory()->orgAdmin($organization)->create();

        $this->actingAs($orgAdmin);

        foreach ($this->superAdminOnlyPages() as $page) {
            $this->get($page)->assertForbidden();
        }
    }

    /**
     * @return list<string>
     */
    private function superAdminOnlyPages(): array
    {
        return [
            route('admin.organizations.index'),
            route('admin.admins.index'),
            route('admin.announcements.index'),
            route('admin.settings.index'),
            route('admin.activities.index'),
        ];
    }

    /**
     * @return list<string>
     */
    private function superAdminPages(): array
    {
        return array_merge([
            route('admin.dashboard'),
            route('admin.registrations.index'),
            route('admin.registrations.pending'),
        ], $this->superAdminOnlyPages());
    }
}
