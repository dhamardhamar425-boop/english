<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

#[Fillable(['key', 'value'])]
class Setting extends Model
{
    /**
     * Kunci pengaturan periode pendaftaran.
     */
    public const REGISTRATION_IS_OPEN = 'registration_is_open';

    public const REGISTRATION_START = 'registration_start';

    public const REGISTRATION_END = 'registration_end';

    /**
     * Cache key untuk seluruh pengaturan.
     */
    private const CACHE_KEY = 'app.settings.all';

    /**
     * Baca nilai pengaturan berdasarkan kunci.
     */
    public static function get(string $key, ?string $default = null): ?string
    {
        $settings = static::all()->keyBy('key');

        return $settings->get($key)?->value ?? $default;
    }

    /**
     * Simpan nilai pengaturan berdasarkan kunci (upsert).
     */
    public static function set(string $key, ?string $value): static
    {
        $setting = static::updateOrCreate(['key' => $key], ['value' => $value]);

        Cache::forget(self::CACHE_KEY);

        return $setting;
    }

    /**
     * Baca seluruh pengaturan dalam satu query (di-cache).
     *
     * @return array<string, string|null>
     */
    public static function allCached(): array
    {
        return Cache::remember(self::CACHE_KEY, now()->addHour(), function (): array {
            return static::query()->pluck('value', 'key')->all();
        });
    }

    /**
     * Ambil nilai dari hasil cache pengaturan.
     */
    public static function cached(string $key, ?string $default = null): ?string
    {
        $settings = static::allCached();

        return $settings[$key] ?? $default;
    }

    /**
     * Apakah periode pendaftaran sedang terbuka saat ini.
     *
     * Periode dianggap terbuka ketika admin membuka pendaftaran DAN waktu
     * sekarang berada di antara tanggal mulai dan tanggal berakhir.
     */
    public static function isRegistrationOpen(): bool
    {
        $state = static::registrationState();

        return $state === 'buka';
    }

    /**
     * Status periode pendaftaran: belum dimulai | buka | ditutup.
     *
     * Pendaftaran juga otomatis dianggap ditutup setelah jadwal pengumuman
     * hasil seleksi tiba, sehingga tidak ada pendaftar baru setelah hasil
     * seleksi diumumkan.
     */
    public static function registrationState(): string
    {
        if (static::cached(self::REGISTRATION_IS_OPEN) !== '1') {
            return 'ditutup';
        }

        $now = Carbon::now(config('app.timezone'))->startOfDay();
        $start = static::parseDate(static::cached(self::REGISTRATION_START));
        $end = static::parseDate(static::cached(self::REGISTRATION_END));

        if ($start !== null && $now->lessThan($start)) {
            return 'belum_dimulai';
        }

        if ($end !== null && $now->greaterThan($end)) {
            return 'ditutup';
        }

        if (Announcement::resultsAreVisible()) {
            return 'ditutup';
        }

        return 'buka';
    }

    /**
     * Tanggal mulai periode pendaftaran.
     */
    public static function registrationStart(): ?Carbon
    {
        return static::parseDate(static::cached(self::REGISTRATION_START));
    }

    /**
     * Tanggal berakhir periode pendaftaran.
     */
    public static function registrationEnd(): ?Carbon
    {
        return static::parseDate(static::cached(self::REGISTRATION_END));
    }

    /**
     * Parse tanggal pengaturan dengan zona waktu aplikasi.
     */
    private static function parseDate(?string $value): ?Carbon
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            return Carbon::parse($value, config('app.timezone'))->startOfDay();
        } catch (\Throwable) {
            return null;
        }
    }
}
