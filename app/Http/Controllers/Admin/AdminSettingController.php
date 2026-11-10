<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\AnnouncementType;
use App\Models\Announcement;
use App\Models\Setting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

/**
 * Pengaturan umum sistem (nama sekolah, periode pendaftaran, dan jadwal
 * pengumuman hasil seleksi serentak) oleh Super Admin.
 */
final class AdminSettingController extends AdminController
{
    /**
     * Judul bawaan pengumuman hasil seleksi serentak.
     */
    private const RESULT_ANNOUNCEMENT_TITLE = 'Pengumuman Hasil Seleksi Anggota Organisasi Siswa';

    /**
     * Tampilkan halaman pengaturan.
     */
    public function __invoke(): View
    {
        $resultAnnouncement = Announcement::activeResultAnnouncement();

        return view('admin.settings.index', [
            'settings' => [
                'school_name' => Setting::cached('school_name', config('app.name')),
                'registration_is_open' => Setting::cached(Setting::REGISTRATION_IS_OPEN, '0'),
                'registration_start' => Setting::cached(Setting::REGISTRATION_START),
                'registration_end' => Setting::cached(Setting::REGISTRATION_END),
            ],
            'resultAnnouncement' => $resultAnnouncement,
            'announcementState' => $resultAnnouncement?->state(),
        ]);
    }

    /**
     * Simpan perubahan pengaturan.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'school_name' => ['required', 'string', 'max:255'],
            'registration_is_open' => ['boolean'],
            'registration_start' => ['required', 'date'],
            'registration_end' => ['required', 'date', 'after_or_equal:registration_start'],
        ], [
            'school_name.required' => 'Nama sekolah wajib diisi.',
            'school_name.max' => 'Nama sekolah maksimal 255 karakter.',
            'registration_start.required' => 'Tanggal mulai wajib diisi.',
            'registration_start.date' => 'Tanggal mulai tidak valid.',
            'registration_end.required' => 'Tanggal berakhir wajib diisi.',
            'registration_end.date' => 'Tanggal berakhir tidak valid.',
            'registration_end.after_or_equal' => 'Tanggal berakhir harus setelah atau sama dengan tanggal mulai.',
        ]);

        Setting::set('school_name', $validated['school_name']);
        Setting::set(Setting::REGISTRATION_IS_OPEN, $request->boolean('registration_is_open') ? '1' : '0');
        Setting::set(Setting::REGISTRATION_START, $validated['registration_start']);
        Setting::set(Setting::REGISTRATION_END, $validated['registration_end']);

        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }

    /**
     * Jadwalkan pengumuman hasil seleksi serentak untuk semua organisasi.
     */
    public function updateResultAnnouncement(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('resultAnnouncement', [
            'announcement_date' => ['required', 'date'],
            'announcement_time' => ['required', 'date_format:H:i'],
            'description' => ['nullable', 'string'],
        ], [
            'announcement_date.required' => 'Tanggal pengumuman wajib diisi.',
            'announcement_date.date' => 'Tanggal pengumuman tidak valid.',
            'announcement_time.required' => 'Waktu pengumuman wajib diisi.',
            'announcement_time.date_format' => 'Format waktu tidak valid (HH:MM).',
        ]);

        $scheduledAt = Carbon::parse(
            $validated['announcement_date'].' '.$validated['announcement_time'],
            config('app.timezone'),
        );

        $this->saveResultAnnouncement([
            'announcement_date' => $validated['announcement_date'],
            'announcement_time' => $validated['announcement_time'].':00',
            'description' => $validated['description'] ?? null,
            'is_published' => $scheduledAt->lessThanOrEqualTo(Carbon::now(config('app.timezone'))),
        ]);

        return back()->with('success', 'Jadwal pengumuman hasil seleksi disimpan dan berlaku serentak untuk semua organisasi.');
    }

    /**
     * Buka pengumuman hasil seleksi sekarang juga untuk semua organisasi.
     */
    public function openResultAnnouncementNow(): RedirectResponse
    {
        $now = Carbon::now(config('app.timezone'));

        $this->saveResultAnnouncement([
            'announcement_date' => $now->toDateString(),
            'announcement_time' => $now->format('H:i:s'),
            'is_published' => true,
        ]);

        return back()->with('success', 'Pengumuman hasil seleksi dibuka sekarang untuk semua organisasi.');
    }

    /**
     * Perbarui pengumuman hasil seleksi yang aktif, atau buat baru bila belum ada.
     *
     * @param  array<string, mixed>  $attributes
     */
    private function saveResultAnnouncement(array $attributes): Announcement
    {
        $announcement = Announcement::activeResultAnnouncement();

        if ($announcement !== null) {
            $announcement->update($attributes);

            return $announcement;
        }

        return Announcement::create([
            ...$attributes,
            'title' => self::RESULT_ANNOUNCEMENT_TITLE,
            'type' => AnnouncementType::Result,
            'created_by' => auth()->id(),
        ]);
    }
}
