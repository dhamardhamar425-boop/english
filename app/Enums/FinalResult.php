<?php

namespace App\Enums;

enum FinalResult: string
{
    case Diterima = 'diterima';
    case Ditolak = 'ditolak';

    /**
     * Label tampilan untuk ditampilkan di view.
     */
    public function label(): string
    {
        return match ($this) {
            self::Diterima => 'Diterima',
            self::Ditolak => 'Ditolak',
        };
    }
}
