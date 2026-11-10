<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRegistrationRequest;
use App\Models\Organization;
use App\Models\Registration;
use App\Models\Setting;
use App\Services\RegistrationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    /**
     * Tampilkan formulir pendaftaran anggota baru untuk organisasi tertentu.
     *
     * Organisasi ditentukan lewat parameter route (slug), sehingga tidak perlu
     * memilih organisasi melalui dropdown di formulir.
     */
    public function create(Request $request, ?Organization $organization = null): View
    {
        return view('registrations.create', [
            'organization' => $organization,
            'registrationOpen' => Setting::isRegistrationOpen(),
            'registrationState' => Setting::registrationState(),
            'registrationStart' => Setting::registrationStart(),
            'registrationEnd' => Setting::registrationEnd(),
        ]);
    }

    /**
     * Simpan pendaftaran siswa dan buat nomor pendaftaran otomatis.
     *
     * ID organisasi diambil dari parameter route (bukan dari input pengguna),
     * sehingga pendaftaran selalu terikat pada organisasi halaman formulirnya.
     */
    public function store(StoreRegistrationRequest $request, RegistrationService $service, ?Organization $organization = null): RedirectResponse
    {
        if (! Setting::isRegistrationOpen()) {
            return back()
                ->withInput()
                ->with('error', 'Pendaftaran belum dibuka atau sudah ditutup. Silakan tunggu informasi selanjutnya.');
        }

        if ($organization === null) {
            return back()
                ->withInput()
                ->with('error', 'Organisasi belum ditentukan. Silakan pilih organisasi dari halaman organisasi.');
        }

        $data = $request->validated();
        $data['organization_id'] = $organization->id;

        $registration = $service->create($data);

        $request->session()->put('registration_success_id', $registration->id);

        return redirect()->route('registrations.success');
    }

    /**
     * Tampilkan konfirmasi pendaftaran berhasil (berbasis sesi, bukan URL publik).
     */
    public function success(Request $request): View|RedirectResponse
    {
        $registrationId = $request->session()->pull('registration_success_id');

        $registration = $registrationId
            ? Registration::with('organization')->find($registrationId)
            : null;

        if ($registration === null) {
            return redirect()
                ->route('home')
                ->with('error', 'Halaman konfirmasi tidak ditemukan. Silakan cek status pendaftaran Anda.');
        }

        return view('registrations.success', [
            'registration' => $registration,
        ]);
    }
}
