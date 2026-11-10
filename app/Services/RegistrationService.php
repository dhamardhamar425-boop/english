<?php

namespace App\Services;

use App\Enums\RegistrationStatus;
use App\Models\Organization;
use App\Models\Registration;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class RegistrationService
{
    /**
     * Jumlah percobaan pembuatan nomor pendaftaran ketika terjadi bentrok.
     */
    private const MAX_ATTEMPTS = 5;

    /**
     * Buat pendaftaran baru lengkap dengan nomor pendaftaran otomatis.
     *
     * Nomor pendaftaran berformat ORGANISASI-TAHUN-URUTAN (contoh: OSIS-2026-0001)
     * dan dijamin unik oleh constraint database.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Registration
    {
        for ($attempt = 1; $attempt <= self::MAX_ATTEMPTS; $attempt++) {
            try {
                return DB::transaction(function () use ($data): Registration {
                    return Registration::create([
                        ...$data,
                        'registration_number' => $this->generateRegistrationNumber((int) $data['organization_id']),
                        'status' => RegistrationStatus::Menunggu,
                        'registered_at' => now(),
                    ]);
                });
            } catch (QueryException $exception) {
                if ($attempt === self::MAX_ATTEMPTS || ! $this->isUniqueViolation($exception)) {
                    throw $exception;
                }

                // Bentrok nomor pendaftaran: coba ulang dengan nomor berikutnya.
            }
        }

        throw new RuntimeException('Gagal membuat nomor pendaftaran unik.');
    }

    /**
     * Susun nomor pendaftaran unik berdasarkan organisasi dan tahun berjalan.
     */
    private function generateRegistrationNumber(int $organizationId): string
    {
        $slug = Organization::query()->whereKey($organizationId)->value('slug') ?? 'ORG';
        $prefix = strtoupper($slug).'-'.now()->year;

        $count = Registration::query()
            ->where('registration_number', 'like', $prefix.'-%')
            ->count();

        return sprintf('%s-%04d', $prefix, $count + 1);
    }

    /**
     * Deteksi pelanggaran constraint unik (MySQL maupun SQLite).
     */
    private function isUniqueViolation(QueryException $exception): bool
    {
        $sqlState = (string) $exception->getCode();

        if (! in_array($sqlState, ['23000', '23505'], true)) {
            return false;
        }

        $message = $exception->getMessage();

        return str_contains($message, 'UNIQUE constraint failed')
            || str_contains($message, 'Duplicate entry')
            || str_contains($message, 'unique constraint');
    }
}
