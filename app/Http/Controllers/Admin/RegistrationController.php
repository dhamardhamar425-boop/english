<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\RegistrationStatus;
use App\Http\Requests\UpdateRegistrationStatusRequest;
use App\Models\Organization;
use App\Models\Registration;
use App\Services\RegistrationStatusService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use RuntimeException;

/**
 * Pengelolaan data pendaftar organisasi siswa di dashboard admin.
 *
 * Admin organisasi otomatis dibatasi hanya pada organisasinya sendiri
 * melalui scope query (dan middleware org_admin untuk route).
 */
final class RegistrationController extends AdminController
{
    /**
     * Daftar seluruh pendaftar dengan pencarian & filter.
     */
    public function index(Request $request): View
    {
        $organizations = Organization::orderBy('name')->get();

        $query = Registration::query()
            ->with(['organization'])
            ->when(! $this->isSuperAdmin(), fn ($q) => $q->where('organization_id', auth()->user()?->organization_id));

        if ($search = trim((string) $request->query('q'))) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('registration_number', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        if ($request->filled('organization') && $this->isSuperAdmin()) {
            $query->where('organization_id', $request->integer('organization'));
        }

        if ($request->filled('status')) {
            $query->where('status', RegistrationStatus::from($request->string('status')->toString()));
        }

        if ($request->filled('result')) {
            $query->where('final_result', $request->string('result')->toString());
        }

        $registrations = $query->latest()->paginate(15)->withQueryString();

        return view('admin.registrations.index', [
            'registrations' => $registrations,
            'organizations' => $organizations,
            'isSuperAdmin' => $this->isSuperAdmin(),
            'filters' => $request->only(['q', 'organization', 'status', 'result']),
        ]);
    }

    /**
     * Pendaftar yang masih menunggu tinjauan.
     */
    public function pending(): View
    {
        $registrations = Registration::query()
            ->with(['organization'])
            ->where('status', RegistrationStatus::Menunggu)
            ->when(! $this->isSuperAdmin(), fn ($q) => $q->where('organization_id', auth()->user()?->organization_id))
            ->latest()
            ->paginate(15);

        return view('admin.registrations.pending', [
            'registrations' => $registrations,
        ]);
    }

    public function __construct(private readonly RegistrationStatusService $statusService) {}

    /**
     * Detail satu pendaftar (termasuk alasan memilih & riwayat status).
     */
    public function show(Registration $registration): View
    {
        $this->authorize('view', $registration);

        $registration->load(['organization', 'statusHistories.admin']);

        return view('admin.registrations.show', [
            'registration' => $registration,
            'statuses' => RegistrationStatus::cases(),
        ]);
    }

    /**
     * Perbarui status & hasil akhir seleksi sebuah pendaftaran.
     */
    public function updateStatus(UpdateRegistrationStatusRequest $request, Registration $registration): RedirectResponse
    {
        $newStatus = RegistrationStatus::from($request->validated('status'));

        try {
            $this->statusService->update(
                registration: $registration,
                newStatus: $newStatus,
                notes: $request->validated('notes'),
                actor: $request->user(),
            );
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', "Status pendaftar {$registration->full_name} diperbarui menjadi {$newStatus->label()}.");
    }
}
