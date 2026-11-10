<?php

namespace App\Models;

use App\Enums\FinalResult;
use App\Enums\RegistrationStatus;
use Database\Factories\RegistrationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'registration_number',
    'full_name',
    'nis',
    'nik',
    'gender',
    'class',
    'phone',
    'address',
    'reason',
    'organization_id',
    'status',
    'final_result',
    'admin_notes',
    'registered_at',
])]
class Registration extends Model
{
    /** @use HasFactory<RegistrationFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => RegistrationStatus::class,
            'final_result' => FinalResult::class,
            'registered_at' => 'datetime',
        ];
    }

    /**
     * Organisasi yang dipilih pendaftar.
     *
     * @return BelongsTo<Organization, $this>
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Riwayat perubahan status pendaftaran.
     *
     * @return HasMany<RegistrationStatusHistory, $this>
     */
    public function statusHistories(): HasMany
    {
        return $this->hasMany(RegistrationStatusHistory::class);
    }

    /**
     * Pendaftar berdasarkan organisasi.
     *
     * @param  Builder<Registration>  $query
     * @return Builder<Registration>
     */
    public function scopeForOrganization(Builder $query, Organization|int $organization): Builder
    {
        $organizationId = $organization instanceof Organization ? $organization->id : $organization;

        return $query->where('organization_id', $organizationId);
    }

    /**
     * Pendaftar dengan status tertentu.
     *
     * @param  Builder<Registration>  $query
     * @return Builder<Registration>
     */
    public function scopeWithStatus(Builder $query, RegistrationStatus $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Hasil akhir yang boleh ditampilkan ke siswa.
     *
     * Hasil Diterima/Ditolak hanya tampil setelah jadwal pengumuman resmi
     * dibuka. Validasi dilakukan di backend, bukan di sisi tampilan.
     */
    public function visibleFinalResult(): ?FinalResult
    {
        if ($this->final_result === null) {
            return null;
        }

        return Announcement::resultsAreVisible() ? $this->final_result : null;
    }
}
