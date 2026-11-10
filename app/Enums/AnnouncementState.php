<?php

namespace App\Enums;

enum AnnouncementState: string
{
    case NotScheduled = 'not_scheduled';
    case NotOpen = 'not_open';
    case Ongoing = 'ongoing';
    case Opened = 'opened';

    /**
     * Label indikator pengumuman untuk ditampilkan ke siswa.
     */
    public function label(): string
    {
        return match ($this) {
            self::NotScheduled => 'Pengumuman belum dijadwalkan',
            self::NotOpen => 'Pengumuman belum dibuka',
            self::Ongoing => 'Pengumuman sedang berlangsung',
            self::Opened => 'Pengumuman sudah dibuka',
        };
    }

    /**
     * Apakah hasil seleksi sudah boleh ditampilkan kepada siswa.
     */
    public function resultsVisible(): bool
    {
        return in_array($this, [self::Ongoing, self::Opened], true);
    }
}
