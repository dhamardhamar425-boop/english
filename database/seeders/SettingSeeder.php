<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Pengaturan awal website: identitas sekolah dan periode pendaftaran.
     *
     * @var array<string, string>
     */
    private const SETTINGS = [
        'school_name' => 'SMK Ma\'arif Nu 1 Ajibarang',
        'school_address' => 'Jl. Pendidikan Nomor 1, Indonesia',
        'school_phone' => '021-555-0100',
        'school_email' => 'info@smk.sch.id',
        'school_tagline' => 'Unggul dalam Prestasi, Berkarakter dalam Perilaku',
        Setting::REGISTRATION_IS_OPEN => '1',
        Setting::REGISTRATION_START => '2026-10-01',
        Setting::REGISTRATION_END => '2026-12-31',
    ];

    /**
     * Simpan pengaturan website.
     */
    public function run(): void
    {
        foreach (self::SETTINGS as $key => $value) {
            Setting::set($key, $value);
        }
    }
}
