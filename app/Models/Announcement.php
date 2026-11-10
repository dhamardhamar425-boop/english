<?php

namespace App\Models;

use App\Enums\AnnouncementState;
use App\Enums\AnnouncementType;
use Database\Factories\AnnouncementFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

#[Fillable([
    'title',
    'description',
    'type',
    'announcement_date',
    'announcement_time',
    'is_published',
    'published_at',
    'created_by',
])]
class Announcement extends Model
{
    /** @use HasFactory<AnnouncementFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => AnnouncementType::class,
            'announcement_date' => 'date',
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    /**
     * Admin pembuat pengumuman.
     *
     * @return BelongsTo<User, $this>
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Jadwal pengumuman hasil seleksi terbaru (berlaku untuk semua organisasi).
     */
    public static function activeResultAnnouncement(): ?self
    {
        return static::query()
            ->where('type', AnnouncementType::Result)
            ->orderByDesc('announcement_date')
            ->orderByDesc('announcement_time')
            ->latest('id')
            ->first();
    }

    /**
     * Waktu pelaksanaan pengumuman dalam zona waktu aplikasi (Asia/Jakarta).
     */
    public function scheduledAt(): ?Carbon
    {
        if ($this->announcement_date === null) {
            return null;
        }

        $time = $this->announcement_time ?: '00:00:00';

        return Carbon::parse(
            $this->announcement_date->toDateString().' '.$time,
            config('app.timezone'),
        );
    }

    /**
     * Akhir hari pelaksanaan pengumuman (batas fase "sedang berlangsung").
     */
    public function scheduledEndAt(): ?Carbon
    {
        return $this->scheduledAt()?->endOfDay();
    }

    /**
     * Indikator status pengumuman ini.
     */
    public function state(): AnnouncementState
    {
        $scheduledAt = $this->scheduledAt();

        if ($scheduledAt === null) {
            return AnnouncementState::NotScheduled;
        }

        $now = Carbon::now(config('app.timezone'));

        if ($now->lessThan($scheduledAt)) {
            return AnnouncementState::NotOpen;
        }

        if ($now->lessThanOrEqualTo($this->scheduledEndAt())) {
            return AnnouncementState::Ongoing;
        }

        return AnnouncementState::Opened;
    }

    /**
     * Indikator status pengumuman hasil seleksi yang sedang berlaku.
     *
     * Hasil akhir seleksi hanya boleh ditampilkan ke siswa ketika status
     * pengumuman sudah "sedang berlangsung" atau "sudah dibuka".
     */
    public static function resultState(): AnnouncementState
    {
        $announcement = static::activeResultAnnouncement();

        if ($announcement === null) {
            return AnnouncementState::NotScheduled;
        }

        return $announcement->state();
    }

    /**
     * Apakah hasil seleksi sudah boleh ditampilkan kepada siswa (cek backend).
     */
    public static function resultsAreVisible(): bool
    {
        return static::resultState()->resultsVisible();
    }

    /**
     * Pengumuman informasi yang tampil di halaman publik.
     *
     * @return Builder<Announcement>
     */
    public function scopePublishedInfo(Builder $query): Builder
    {
        return $query
            ->where('type', AnnouncementType::Info)
            ->where('is_published', true)
            ->orderByDesc('announcement_date')
            ->orderByDesc('id');
    }
}
