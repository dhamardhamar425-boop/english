<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Organization;
use App\Models\Registration;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DatabaseSeedingTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    /**
     * Seeder harus membuat tepat lima organisasi dengan link grup unik.
     */
    public function test_seeder_creates_five_organizations_with_unique_whatsapp_links(): void
    {
        $this->seed();

        $organizations = Organization::all();

        $this->assertCount(5, $organizations);
        $this->assertSame(
            ['ipnu-ippnu', 'osis', 'paskohamas', 'pks', 'pmr'],
            $organizations->pluck('slug')->sort()->values()->all(),
        );
        $this->assertCount(5, $organizations->pluck('whatsapp_group_link')->unique());
        $this->assertTrue($organizations->every(fn (Organization $organization) => $organization->is_active));
    }

    /**
     * Seeder harus membuat satu Super Admin dan lima Admin Organisasi.
     */
    public function test_seeder_creates_six_admin_accounts(): void
    {
        $this->seed();

        $this->assertSame(6, User::count());
        $this->assertSame(1, User::where('role', UserRole::SuperAdmin)->count());
        $this->assertSame(5, User::where('role', UserRole::OrgAdmin)->count());
        $this->assertNull(User::where('role', UserRole::SuperAdmin)->first()->organization_id);
        $this->assertSame(
            5,
            User::where('role', UserRole::OrgAdmin)->whereNotNull('organization_id')->count(),
        );
    }

    /**
     * NIS dan NIK harus dicegah duplikasinya oleh constraint database.
     */
    public function test_registration_nis_and_nik_must_be_unique(): void
    {
        $organization = Organization::factory()->create();

        Registration::factory()->create([
            'organization_id' => $organization->id,
            'nis' => '12345',
            'nik' => '3200000000000001',
            'registration_number' => 'REG-001',
        ]);

        $this->expectException(QueryException::class);

        DB::table('registrations')->insert([
            'registration_number' => 'REG-002',
            'full_name' => 'Siswa Duplikat',
            'nis' => '12345',
            'nik' => '3200000000000002',
            'gender' => 'Laki-laki',
            'class' => 'X-1',
            'major' => 'Akuntansi',
            'phone' => '081234567890',
            'address' => 'Alamat',
            'reason' => 'Alasan',
            'organization_id' => $organization->id,
            'status' => 'menunggu',
            'registered_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Periode pendaftaran harus mengikuti pengaturan yang dikelola Super Admin.
     */
    public function test_registration_period_state_follows_settings(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-11-10 08:00:00', config('app.timezone')));

        $this->seed();

        $this->assertSame('buka', Setting::registrationState());
        $this->assertTrue(Setting::isRegistrationOpen());

        Setting::set(Setting::REGISTRATION_IS_OPEN, '0');

        $this->assertSame('ditutup', Setting::registrationState());
        $this->assertFalse(Setting::isRegistrationOpen());

        Setting::set(Setting::REGISTRATION_IS_OPEN, '1');
        Setting::set(Setting::REGISTRATION_START, now()->addDays(5)->toDateString());

        $this->assertSame('belum_dimulai', Setting::registrationState());
    }
}
