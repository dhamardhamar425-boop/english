<?php

namespace App\Enums;

enum AnnouncementType: string
{
    case Result = 'result';
    case Info = 'info';

    /**
     * Label tampilan untuk ditampilkan di view.
     */
    public function label(): string
    {
        return match ($this) {
            self::Result => 'Jadwal Pengumuman Hasil',
            self::Info => 'Pengumuman Umum',
        };
    }
}
