<?php

namespace App\Models;

use Database\Factories\OrganizationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'name',
    'slug',
    'logo',
    'description',
    'vision',
    'mission',
    'program_work',
    'activities',
    'requirements',
    'whatsapp_group_link',
    'contact_link',
    'is_active',
])]
class Organization extends Model
{
    /** @use HasFactory<OrganizationFactory> */
    use HasFactory;

    /**
     * Pendaftaran siswa ke organisasi ini.
     *
     * @return HasMany<Registration, $this>
     */
    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    /**
     * Kegiatan organisasi.
     *
     * @return HasMany<Activity, $this>
     */
    public function organizationActivities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    /**
     * Galeri dokumentasi organisasi.
     *
     * @return HasMany<Gallery, $this>
     */
    public function galleries(): HasMany
    {
        return $this->hasMany(Gallery::class);
    }

    /**
     * Akun admin organisasi ini.
     *
     * @return HasMany<User, $this>
     */
    public function admins(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Batasi query hanya pada organisasi yang aktif.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Warna identitas organisasi (dipakai untuk grafik & badge).
     *
     * Dipetakan berdasarkan slug agar konsisten tanpa perlu kolom database.
     */
    public function getColorAttribute(): string
    {
        return match ($this->slug) {
            'osis' => '#1e3a8a',
            'ipnu-ippnu' => '#059669',
            'pmr' => '#dc2626',
            'pks' => '#6b7280',
            'paskohamas' => '#92400e',
            default => sprintf('#%06x', crc32((string) $this->slug) & 0xFFFFFF),
        };
    }

    /**
     * URL logo organisasi pada disk publik, null jika belum ada logo.
     */
    public function logoUrl(): ?string
    {
        if ($this->logo === null || $this->logo === '') {
            return null;
        }

        return Storage::disk('public')->url($this->logo);
    }

    /**
     * Inisial nama organisasi untuk fallback logo.
     */
    public function initials(): string
    {
        $words = preg_split('/[\s\-]+/', mb_trim($this->name));

        if ($words === false || $words === []) {
            return mb_substr($this->name, 0, 2);
        }

        if (count($words) === 1) {
            return mb_strtoupper(mb_substr($words[0], 0, 2));
        }

        return mb_strtoupper(mb_substr($words[0], 0, 1).mb_substr($words[1], 0, 1));
    }
}
