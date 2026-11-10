<?php

namespace Database\Seeders;

use App\Models\Organization;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class OrganizationSeeder extends Seeder
{
    /**
     * Lima organisasi siswa yang menjadi pilihan pendaftaran.
     *
     * @return array<int, array<string, mixed>>
     */
    private const ORGANIZATIONS = [
        [
            'name' => 'OSIS',
            'slug' => 'osis',
            'logo' => 'logos/osis.svg',
            'description' => 'Organisasi Siswa Intra Sekolah (OSIS) adalah wadah organisasi siswa tingkat sekolah yang menjadi pusat kreativitas, kepemimpinan, dan pengabdian siswa dalam membangun sekolah.',
            'vision' => 'Terwujudnya siswa yang berdedikasi, berjiwa pemimpin, dan aktif membangun sekolah yang unggul serta berkarakter.',
            'mission' => "1. Menumbuhkan jiwa kepemimpinan dan kreativitas siswa.\n2. Menjadi jembatan aspirasi seluruh siswa kepada pihak sekolah.\n3. Menyelenggarakan kegiatan akademik, olahraga, dan seni yang positif.\n4. Membangun rasa persatuan dan kekeluargaan antar siswa.",
            'program_work' => "1. Bulan Orientasi Siswa (BOS) bersama angkatan baru.\n2. Rapat Kerja OSIS dan penyusunan program tahunan.\n3. Pekan Olahraga dan Seni Antar Kelas.\n4. Aksi Sosial dan Bakti Sosial Masyarakat.\n5. Pemilihan Ketua OSIS periode berikutnya.",
            'activities' => 'Rapat koordinasi mingguan, kegiatan piket keamanan sekolah bersama PKS, donor darah bersama PMR, lomba antar kelas, dan persiapan upacara bendera.',
            'requirements' => "1. Siswa aktif kelas X atau XI dengan bukti kartu pelajar.\n2. Tidak sedang dalam sanksi disiplin sekolah.\n3. Bersedia menghadiri seleksi offline sesuai jadwal.\n4. Bersedia mematuhi seluruh peraturan dan program kerja organisasi.\n5. Mengisi formulir pendaftaran dengan data yang benar.",
            'whatsapp_group_link' => 'https://chat.whatsapp.com/OSIS2026Pendaftaran',
            'contact_link' => 'https://wa.me/6281110001001',
        ],
        [
            'name' => 'IPNU-IPPNU',
            'slug' => 'ipnu-ippnu',
            'logo' => 'logos/ipnu-ippnu.svg',
            'description' => 'Ikatan Pelajar Nahdlatul Ulama - Ikatan Pelajar Putri Nahdlatul Ulama (IPNU-IPPNU) adalah organisasi pelajar yang berlandaskan nilai Ahlussunnah Waljamaah dengan semangat intelektual, religius, dan sosial.',
            'vision' => 'Menjadi pelajar muslim yang berilmu, berakhlak mulia, dan berkontribusi nyata bagi masyarakat.',
            'mission' => "1. Menanamkan nilai keislaman dan akhlakul karimah dalam kehidupan sehari-hari.\n2. Mengembangkan potensi intelektual melalui kajian dan diskusi.\n3. Mempererat tali silaturahmi antar pelajar.\n4. Melatih keterampilan organisasi dan kepemimpinan.",
            'program_work' => "1. Kajian keislaman rutin mingguan.\n2. Pelatihan kaderisasi dan keorganisasian.\n3. Bakti sosial dan kegiatan lingkungan.\n4. Lomba pidato dan tulis ilmiah antar pelajar.\n5. Ziarah dan santunan anak yatim.",
            'activities' => 'Pengajian rutin, kajian kitab kuning dasar, latihan kepemimpinan, bakti sosial ke panti asuhan, dan peringatan hari besar Islam sekolah.',
            'requirements' => "1. Siswa aktif kelas X atau XI dengan bukti kartu pelajar.\n2. Bersedia mengikuti kajian dan kegiatan organisasi secara rutin.\n3. Bersedia menghadiri seleksi offline sesuai jadwal.\n4. Bersikap sopan dan menjaga nama baik organisasi.\n5. Mengisi formulir pendaftaran dengan data yang benar.",
            'whatsapp_group_link' => 'https://chat.whatsapp.com/IPNU2026Pendaftaran',
            'contact_link' => 'https://wa.me/6281110001002',
        ],
        [
            'name' => 'PMR',
            'slug' => 'pmr',
            'logo' => 'logos/pmr.svg',
            'description' => 'Palang Merah Remaja (PMR) adalah wadah pembinaan siswa dalam bidang kesehatan dan pertolongan pertama serta pelayanan kemanusiaan di lingkungan sekolah.',
            'vision' => 'Terwujudnya siswa yang peduli kesehatan, terampil memberikan pertolongan pertama, dan berjiwa kemanusiaan.',
            'mission' => "1. Melatih keterampilan pertolongan pertama dan p3k.\n2. Meningkatkan kepedulian terhadap kesehatan sekolah.\n3. Menumbuhkan semangat relawan dan pelayanan kemanusiaan.\n4. Bekerja sama dengan PMI kota dalam kegiatan sosial.",
            'program_work' => "1. Pelatihan pertolongan pertama dan P3k.\n2. Posko kesehatan saat kegiatan sekolah.\n3. Donor darah bersama PMI.\n4. Simulasi evakuasi bencana.\n5. Pemeriksaan kesehatan berkala siswa.",
            'activities' => 'Latihan perban dan transportasi korban, pendirian posko saat olahraga, kerja sama donor darah, dan penyuluhan hidup sehat.',
            'requirements' => "1. Siswa aktif kelas X atau XI dengan bukti kartu pelajar.\n2. Sehat jasmani dan rohani.\n3. Bersedia mengikuti pelatihan dan kegiatan kemanusiaan.\n4. Bersedia menghadiri seleksi offline sesuai jadwal.\n5. Mengisi formulir pendaftaran dengan data yang benar.",
            'whatsapp_group_link' => 'https://chat.whatsapp.com/PMR2026Pendaftaran',
            'contact_link' => 'https://wa.me/6281110001003',
        ],
        [
            'name' => 'PKS',
            'slug' => 'pks',
            'logo' => 'logos/pks.svg',
            'description' => 'Patroli Keamanan Sekolah (PKS) adalah organisasi siswa yang bertugas menjaga ketertiban, keamanan, dan kenyamanan lingkungan sekolah bersama petugas keamanan.',
            'vision' => 'Terwujudnya lingkungan sekolah yang aman, tertib, dan nyaman bagi seluruh warga sekolah.',
            'mission' => "1. Menjaga ketertiban saat jam belajar dan kegiatan sekolah.\n2. Membantu mengatur arus lalu lintas dan penyeberangan siswa.\n3. Membangun disiplin dan budaya tertib siswa.\n4. Bekerja sama dengan guru piket dan petugas keamanan.",
            'program_work' => "1. Pemilihan dan pelatihan anggota PKS.\n2. Piket keamanan pagi dan pulang sekolah.\n3. Operasi tertib seragam dan kelengkapan.\n4. Simulasi tanggap darurat sekolah.\n5. Apel keamanan mingguan.",
            'activities' => 'Piket gerbang pagi, pengaturan penyeberangan, pendampingan upacara, patroli ketertiban, dan kerja sama dengan satpam sekolah.',
            'requirements' => "1. Siswa aktif kelas X atau XI dengan bukti kartu pelajar.\n2. Bersedia hadir lebih awal untuk piket keamanan.\n3. Disiplin, jujur, dan bertanggung jawab.\n4. Bersedia menghadiri seleksi offline sesuai jadwal.\n5. Mengisi formulir pendaftaran dengan data yang benar.",
            'whatsapp_group_link' => 'https://chat.whatsapp.com/PKS2026Pendaftaran',
            'contact_link' => 'https://wa.me/6281110001004',
        ],
        [
            'name' => 'PASKOHAMAS',
            'slug' => 'paskohamas',
            'logo' => 'logos/paskohamas.svg',
            'description' => 'PASKOHAMAS adalah pasukan kehormatan sekolah yang bertugas pada setiap upacara bendera dan kegiatan resmi sekolah dengan latihan ketahanan fisik dan kedisiplinan tinggi.',
            'vision' => 'Menjadi pasukan kehormatan sekolah yang disiplin, tangguh, dan menginspirasi seluruh siswa.',
            'mission' => "1. Melatih kedisiplinan dan ketahanan fisik anggota.\n2. Menjadi pengawal pengibaran bendera dalam upacara.\n3. Menumbuhkan rasa cinta tanah air dan nasionalisme.\n4. Menjadi teladan perilaku tertib bagi siswa lain.",
            'program_work' => "1. Latihan dasar baris berbaris dan pengibaran bendera.\n2. Pemilihan dan kaderisasi anggota baru.\n3. Pelatihan fisik rutin pagi hari.\n4. Penugasan upacara bendera hari senin.\n5. Penampilan pada upacara dan acara resmi sekolah.",
            'activities' => 'Latihan baris berbaris, latihan fisik pagi, latihan pengibaran bendera, dan penugasan upacara serta acara resmi sekolah.',
            'requirements' => "1. Siswa aktif kelas X atau XI dengan bukti kartu pelajar.\n2. Bersedia mengikuti latihan fisik secara rutin.\n3. Tinggi badan proporsional dan sehat jasmani.\n4. Bersedia menghadiri seleksi offline sesuai jadwal.\n5. Mengisi formulir pendaftaran dengan data yang benar.",
            'whatsapp_group_link' => 'https://chat.whatsapp.com/PASKO2026Pendaftaran',
            'contact_link' => 'https://wa.me/6281110001005',
        ],
    ];

    /**
     * Seed the database with the five student organizations.
     */
    public function run(): void
    {
        foreach (self::ORGANIZATIONS as $organization) {
            $this->copyLogoToPublicDisk($organization['logo']);

            Organization::updateOrCreate(
                ['slug' => $organization['slug']],
                $organization,
            );
        }
    }

    /**
     * Salin logo placeholder ke disk publik agar tersedia untuk pendaftaran.
     */
    private function copyLogoToPublicDisk(string $logoPath): void
    {
        if (Storage::disk('public')->exists($logoPath)) {
            return;
        }

        $source = database_path('seeders/data/'.$logoPath);

        if (! is_file($source)) {
            return;
        }

        Storage::disk('public')->put($logoPath, file_get_contents($source));
    }
}
