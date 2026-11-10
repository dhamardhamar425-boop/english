<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\RegistrationStatus;
use App\Models\Announcement;
use App\Models\AuditLog;
use App\Models\Registration;
use App\Models\RegistrationStatusHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Layanan perubahan status & hasil akhir seleksi pendaftar.
 *
 * Aturan bisnis yang ditegakkan di sini (bukan di tampilan):
 * - Satu pendaftar hanya dapat berpindah status secara wajar
 *   (menunggu -> seleksi -> diterima/ditolak).
 * - Perubahan hasil akhir SETELAH pengumuman resmi dibuka hanya boleh
 *   dilakukan Super Admin, dan wajib meninggalkan jejak audit.
 */
final class RegistrationStatusService
{
    /**
     * Perbarui status (dan hasil akhir) sebuah pendaftaran.
     *
     * @param  array{status: string, notes?: string|null}  $data
     *
     * @throws RuntimeException ketika transisi tidak diizinkan.
     */
    public function update(Registration $registration, RegistrationStatus $newStatus, ?string $notes, User $actor): Registration
    {
        $oldStatus = $registration->status;

        $this->guardTransition($oldStatus, $newStatus);
        $this->guardFinalResultChange($registration, $newStatus, $actor);

        $isFinal = in_array($newStatus, RegistrationStatus::finalStatuses(), true);

        return DB::transaction(function () use ($registration, $oldStatus, $newStatus, $notes, $actor, $isFinal): Registration {
            $registration->update([
                'status' => $newStatus,
                'final_result' => $isFinal ? $newStatus->value : null,
            ]);

            RegistrationStatusHistory::create([
                'registration_id' => $registration->id,
                'admin_id' => $actor->id,
                'old_status' => $oldStatus?->value,
                'new_status' => $newStatus->value,
                'notes' => $notes,
            ]);

            AuditLog::record(
                action: 'ubah_status_pendaftar',
                description: sprintf(
                    'Status pendaftar %s (%s) diubah dari %s menjadi %s%s.',
                    $registration->registration_number,
                    $registration->full_name,
                    $oldStatus?->label() ?? 'Belum ada',
                    $newStatus->label(),
                    $notes ? " Catatan: {$notes}" : '',
                ),
                auditable: $registration,
            );

            return $registration->refresh();
        });
    }

    /**
     * Pastikan transisi status masuk akal secara alur seleksi.
     */
    private function guardTransition(?RegistrationStatus $oldStatus, RegistrationStatus $newStatus): void
    {
        if ($oldStatus === $newStatus) {
            return;
        }

        $allowed = match ($oldStatus) {
            RegistrationStatus::Menunggu => [
                RegistrationStatus::Seleksi,
                RegistrationStatus::Diterima,
                RegistrationStatus::Ditolak,
            ],
            RegistrationStatus::Seleksi => [
                RegistrationStatus::Diterima,
                RegistrationStatus::Ditolak,
                RegistrationStatus::Menunggu,
            ],
            default => RegistrationStatus::cases(),
        };

        if (! in_array($newStatus, $allowed, true)) {
            throw new RuntimeException(
                "Transisi status dari {$oldStatus?->label()} ke {$newStatus->label()} tidak diizinkan.",
            );
        }
    }

    /**
     * Setelah pengumuman hasil dibuka, hanya Super Admin yang boleh
     * mengubah hasil akhir pendaftar. Perubahan apa pun wajib diaudit.
     */
    private function guardFinalResultChange(Registration $registration, RegistrationStatus $newStatus, User $actor): void
    {
        $changesFinalResult = $newStatus !== $registration->status;

        if (! $changesFinalResult || ! Announcement::resultsAreVisible()) {
            return;
        }

        if (! $actor->isSuperAdmin()) {
            throw new RuntimeException(
                'Hasil seleksi sudah diumumkan. Perubahan hasil akhir hanya dapat dilakukan Super Admin.',
            );
        }
    }
}
