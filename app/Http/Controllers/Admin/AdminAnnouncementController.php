<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Models\Announcement;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Pengelolaan pengumuman (hasil seleksi & informasi) oleh Super Admin.
 *
 * Hanya Super Admin yang boleh mengelola pengumuman (middleware super_admin).
 */
final class AdminAnnouncementController extends AdminController
{
    /**
     * Daftar seluruh pengumuman.
     */
    public function index(): View
    {
        return view('admin.announcements.index', [
            'announcements' => Announcement::query()
                ->with('createdBy')
                ->orderByDesc('announcement_date')
                ->orderByDesc('announcement_time')
                ->paginate(15),
        ]);
    }

    /**
     * Simpan pengumuman baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'in:result,info'],
            'announcement_date' => ['required', 'date'],
            'announcement_time' => ['required', 'date_format:H:i'],
            'is_published' => ['boolean'],
        ], [
            'title.required' => 'Judul pengumuman wajib diisi.',
            'title.max' => 'Judul pengumuman maksimal 255 karakter.',
            'type.required' => 'Jenis pengumuman wajib dipilih.',
            'type.in' => 'Jenis pengumuman tidak valid.',
            'announcement_date.required' => 'Tanggal pengumuman wajib diisi.',
            'announcement_date.date' => 'Tanggal pengumuman tidak valid.',
            'announcement_time.required' => 'Waktu pengumuman wajib diisi.',
            'announcement_time.date_format' => 'Format waktu tidak valid (HH:MM).',
        ]);

        Announcement::create([
            ...$validated,
            'announcement_time' => $validated['announcement_time'].':00',
            'is_published' => (bool) ($validated['is_published'] ?? false),
            'created_by' => auth()->id(),
        ]);

        return back()->with('success', 'Pengumuman berhasil dibuat.');
    }

    /**
     * Perbarui pengumuman.
     */
    public function update(Request $request, Announcement $announcement): RedirectResponse
    {
        $validated = $request->validateWithBag('announcementUpdate', [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'in:result,info'],
            'announcement_date' => ['required', 'date'],
            'announcement_time' => ['required', 'date_format:H:i'],
            'is_published' => ['boolean'],
        ], [
            'title.required' => 'Judul pengumuman wajib diisi.',
            'type.required' => 'Jenis pengumuman wajib dipilih.',
            'announcement_date.required' => 'Tanggal pengumuman wajib diisi.',
            'announcement_time.required' => 'Waktu pengumuman wajib diisi.',
        ]);

        $announcement->update([
            ...$validated,
            'announcement_time' => $validated['announcement_time'].':00',
            'is_published' => (bool) ($validated['is_published'] ?? false),
        ]);

        return back()->with('success', 'Pengumuman berhasil diperbarui.');
    }

    /**
     * Hapus pengumuman.
     */
    public function destroy(Announcement $announcement): RedirectResponse
    {
        $announcement->delete();

        return back()->with('success', 'Pengumuman berhasil dihapus.');
    }
}
