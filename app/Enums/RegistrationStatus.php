<?php

namespace App\Enums;

enum RegistrationStatus: string
{
    case Menunggu = 'menunggu';
    case Seleksi = 'seleksi';
    case Diterima = 'diterima';
    case Ditolak = 'ditolak';

    /**
     * Label tampilan untuk ditampilkan di view.
     */
    public function label(): string
    {
        return match ($this) {
            self::Menunggu => 'Menunggu',
            self::Seleksi => 'Seleksi',
            self::Diterima => 'Diterima',
            self::Ditolak => 'Ditolak',
        };
    }

    /**
     * Status yang merupakan hasil akhir seleksi.
     *
     * @return array<int, self>
     */
    public static function finalStatuses(): array
    {
        return [self::Diterima, self::Ditolak];
    }

    /**
     * Status yang masih diproses (belum ada hasil akhir).
     */
    public function isPending(): bool
    {
        return in_array($this, [self::Menunggu, self::Seleksi], true);
    }
}
