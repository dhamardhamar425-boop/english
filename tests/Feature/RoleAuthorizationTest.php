<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RoleAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Daftarkan rute uji untuk middleware pembatasan peran.
     */
    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware(['auth', 'super_admin'])->get('/uji/super-admin', fn () => 'super-ok');
        Route::middleware(['auth', 'org_admin'])->get('/uji/org-admin', fn () => 'org-ok');
    }

    /**
     * Super Admin dapat mengakses rute khusus Super Admin.
     */
    public function test_super_admin_can_access_super_admin_routes(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($superAdmin)->get('/uji/super-admin')->assertOk()->assertSee('super-ok');
    }

    /**
     * Admin Organisasi DILARANG mengakses rute khusus Super Admin (403).
     */
    public function test_org_admin_cannot_access_super_admin_routes(): void
    {
        $organization = Organization::factory()->create();
        $orgAdmin = User::factory()->orgAdmin($organization)->create();

        $this->actingAs($orgAdmin)->get('/uji/super-admin')->assertForbidden();
    }

    /**
     * Admin Organisasi dapat mengakses rute khusus Admin Organisasi.
     */
    public function test_org_admin_can_access_org_admin_routes(): void
    {
        $organization = Organization::factory()->create();
        $orgAdmin = User::factory()->orgAdmin($organization)->create();

        $this->actingAs($orgAdmin)->get('/uji/org-admin')->assertOk()->assertSee('org-ok');
    }

    /**
     * Super Admin TIDAK dianggap Admin Organisasi untuk rute org admin.
     */
    public function test_super_admin_is_not_org_admin(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($superAdmin)->get('/uji/org-admin')->assertForbidden();
    }

    /**
     * Admin Organisasi hanya dapat melihat pendaftar organisasinya sendiri.
     */
    public function test_org_admin_can_only_view_own_organization_registrations(): void
    {
        $osis = Organization::factory()->create(['slug' => 'osis']);
        $pmr = Organization::factory()->create(['slug' => 'pmr']);

        $registrationOsis = Registration::factory()->create(['organization_id' => $osis->id]);
        $registrationPmr = Registration::factory()->create(['organization_id' => $pmr->id]);

        $adminOsis = User::factory()->orgAdmin($osis)->create();
        $adminPmr = User::factory()->orgAdmin($pmr)->create();

        $this->assertTrue($adminOsis->can('view', $registrationOsis));
        $this->assertFalse($adminOsis->can('view', $registrationPmr));

        $this->assertTrue($adminPmr->can('view', $registrationPmr));
        $this->assertFalse($adminPmr->can('view', $registrationOsis));
    }

    /**
     * Super Admin dapat melihat seluruh pendaftar lintas organisasi.
     */
    public function test_super_admin_can_view_all_registrations(): void
    {
        $osis = Organization::factory()->create();
        $pmr = Organization::factory()->create();

        $registrationOsis = Registration::factory()->create(['organization_id' => $osis->id]);
        $registrationPmr = Registration::factory()->create(['organization_id' => $pmr->id]);

        $superAdmin = User::factory()->superAdmin()->create();

        $this->assertTrue($superAdmin->can('view', $registrationOsis));
        $this->assertTrue($superAdmin->can('view', $registrationPmr));
        $this->assertTrue($superAdmin->can('update', $registrationPmr));
    }

    /**
     * Admin Organisasi hanya dapat memperbarui organisasi miliknya sendiri.
     */
    public function test_org_admin_can_only_update_own_organization(): void
    {
        $osis = Organization::factory()->create();
        $pmr = Organization::factory()->create();

        $adminOsis = User::factory()->orgAdmin($osis)->create();

        $this->assertTrue($adminOsis->can('update', $osis));
        $this->assertFalse($adminOsis->can('update', $pmr));
        $this->assertFalse($adminOsis->can('delete', $osis));
    }
}
