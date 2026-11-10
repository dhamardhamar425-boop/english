<?php

namespace App\Http\Controllers;

use App\Enums\AnnouncementState;
use App\Enums\RegistrationStatus;
use App\Http\Requests\StatusCheckRequest;
use App\Models\Announcement;
use App\Models\Registration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class StatusCheckController extends Controller
{
    /**
     * Tampilkan formulir cek status pendaftaran.
     */
    public function create(): View
    {
        return view('status-check.create', [
            'resultAnnouncement' => Announcement::activeResultAnnouncement(),
            'announcementState' => Announcement::resultState(),
        ]);
    }

    /**
     * Cari pendaftaran berdasarkan nomor pendaftaran atau NIS.
     *
     * Pencarian dibatasi (rate limit) untuk mencegah penyalahgunaan data.
     */
    public function store(StatusCheckRequest $request): RedirectResponse
    {
        $identifier = $request->validated()['identifier'];

        $registration = Registration::query()
            ->where('registration_number', $identifier)
            ->orWhere('nis', $identifier)
            ->first();

        if ($registration === null) {
            return back()
                ->withInput()
                ->withErrors([
                    'identifier' => 'Data pendaftaran tidak ditemukan. Periksa kembali nomor pendaftaran atau NIS Anda.',
                ]);
        }

        $request->session()->put('status_check_registration_id', $registration->id);

        return redirect()->route('status.result');
    }

    /**
     * Tampilkan profil status pendaftaran siswa dari sesi pencarian.
     */
    public function result(Request $request): View|RedirectResponse
    {
        $registrationId = $request->session()->get('status_check_registration_id');

        $registration = $registrationId
            ? Registration::with(['organization', 'statusHistories'])->find($registrationId)
            : null;

        if ($registration === null) {
            return redirect()
                ->route('status.check')
                ->with('error', 'Silakan lakukan pencarian terlebih dahulu untuk melihat status pendaftaran.');
        }

        $announcementState = Announcement::resultState();

        return view('status-check.result', [
            'registration' => $registration,
            'resultAnnouncement' => Announcement::activeResultAnnouncement(),
            'announcementState' => $announcementState,
            'visibleStatus' => $this->visibleStatus($registration, $announcementState),
            'timeline' => $this->publicTimeline($registration, $announcementState),
        ]);
    }

    /**
     * Status yang boleh ditampilkan ke siswa.
     *
     * Hasil Diterima/Ditolak disembunyikan sampai pengumuman resmi dibuka.
     */
    private function visibleStatus(Registration $registration, AnnouncementState $state): string
    {
        $status = $registration->status;

        if ($status === RegistrationStatus::Menunggu || $status === RegistrationStatus::Seleksi) {
            return $status->value;
        }

        return $state->resultsVisible() ? $status->value : 'belum_diumumkan';
    }

    /**
     * Garis waktu seleksi yang aman ditampilkan ke publik.
     *
     * @return array<int, array{date: Carbon, label: string, status: string}>
     */
    private function publicTimeline(Registration $registration, AnnouncementState $state): array
    {
        $timeline = [[
            'date' => $registration->registered_at,
            'label' => 'Pendaftaran diterima sistem',
            'status' => RegistrationStatus::Menunggu->value,
        ]];

        $histories = $registration->statusHistories()
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        foreach ($histories as $history) {
            $newStatus = $history->new_status;

            if ($newStatus === null) {
                continue;
            }

            // Hasil akhir tidak boleh bocor lewat timeline sebelum pengumuman.
            if (! $newStatus->isPending() && ! $state->resultsVisible()) {
                continue;
            }

            $timeline[] = [
                'date' => $history->created_at,
                'label' => match ($newStatus) {
                    RegistrationStatus::Seleksi => 'Masuk tahap seleksi offline',
                    RegistrationStatus::Diterima => 'Dinyatakan lolos seleksi',
                    RegistrationStatus::Ditolak => 'Tidak lolos seleksi',
                    default => 'Status diperbarui',
                },
                'status' => $newStatus->value,
            ];
        }

        return $timeline;
    }
}
