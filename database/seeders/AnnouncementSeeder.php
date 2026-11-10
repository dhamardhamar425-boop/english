<?php

namespace Database\Seeders;

use App\Enums\AnnouncementType;
use App\Models\Announcement;
use App\Models\User;
use Illuminate\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    /**
     * Jadwal pengumuman hasil seleksi serentak dan pengumuman umum.
     *
     * @return array<int, array<string, mixed>>
     */
    private const ANNOUNCEMENTS = [
        [
            'type' => AnnouncementType::Result,
            'title' => 'Pengumuman Hasil Seleksi Anggota Organisasi Siswa',
            'description' => 'Hasil akhir seleksi anggota baru kelima organisasi (OSIS, IPNU-IPPNU, PMR, PKS, dan PASKOHAMAS) diumumkan serentak. Seluruh calon anggota dapat mengecek hasilnya melalui halaman Cek Status Pendaftaran menggunakan nomor pendaftaran atau NIS.',
            'announcement_date' => '2026-11-15',
            'announcement_time' => '13:00:00',
            'is_published' => false,
        ],
        [
            'type' => AnnouncementType::Info,
            'title' => 'Pendaftaran Anggota Baru Organisasi Siswa Dibuka',
            'description' => 'Pendaftaran anggota baru kelima organisasi siswa telah dibuka. Siswa hanya dapat memilih satu organisasi. Daftarkan diri Anda melalui halaman Pendaftaran sebelum periode ditutup.',
            'announcement_date' => '2026-10-01',
            'announcement_time' => '08:00:00',
            'is_published' => true,
        ],
        [
            'type' => AnnouncementType::Info,
            'title' => 'Pelaksanaan Seleksi Offline',
            'description' => 'Seleksi dilakukan secara offline di lingkungan sekolah sesuai jadwal masing-masing organisasi. Pastikan Anda mengikuti seluruh tahapan seleksi dan tetap memantau pengumuman resmi di website ini.',
            'announcement_date' => '2026-11-03',
            'announcement_time' => '09:00:00',
            'is_published' => true,
        ],
    ];

    /**
     * Buat jadwal pengumuman dan pengumuman umum.
     */
    public function run(): void
    {
        $superAdmin = User::where('email', 'admin@smk.sch.id')->first();

        foreach (self::ANNOUNCEMENTS as $announcement) {
            $exists = Announcement::query()
                ->where('type', $announcement['type'])
                ->where('title', $announcement['title'])
                ->exists();

            if ($exists) {
                continue;
            }

            Announcement::create([
                ...$announcement,
                'created_by' => $superAdmin?->id,
                'published_at' => $announcement['is_published'] ? now() : null,
            ]);
        }
    }
}
