<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Requests\StoreActivityRequest;
use App\Http\Requests\UpdateActivityRequest;
use App\Models\Activity;
use App\Models\Organization;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Pengelolaan kegiatan organisasi (agenda & dokumentasi) oleh admin.
 *
 * Admin organisasi hanya boleh mengelola kegiatan organisasinya sendiri
 * (dijamin oleh ActivityPolicy + scoping di controller).
 */
final class ActivityController extends AdminController
{
    /**
     * Daftar kegiatan — seluruh organisasi (super admin) atau organisasi
     * sendiri (admin organisasi).
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Activity::class);

        $organization = $this->activeOrganization();

        $activities = Activity::query()
            ->with('organization')
            ->when(
                $organization !== null,
                fn ($query) => $query->where('organization_id', $organization->id),
            )
            ->when(
                $this->isSuperAdmin() && $request->filled('organization_id'),
                fn ($query) => $query->where('organization_id', $request->integer('organization_id')),
            )
            ->latest('occurred_at')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.activities.index', [
            'activities' => $activities,
            'organizations' => $this->isSuperAdmin()
                ? Organization::query()->orderBy('name')->get()
                : collect(),
            'activeOrganizationFilter' => $request->integer('organization_id'),
        ]);
    }

    /**
     * Simpan kegiatan baru.
     */
    public function store(StoreActivityRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        Activity::create([
            ...$validated,
            'organization_id' => $validated['organization_id'] ?? $this->activeOrganization()?->id,
        ]);

        return back()->with('success', 'Kegiatan berhasil ditambahkan.');
    }

    /**
     * Perbarui kegiatan.
     */
    public function update(UpdateActivityRequest $request, Activity $activity): RedirectResponse
    {
        $activity->update($request->validated());

        return back()->with('success', 'Kegiatan berhasil diperbarui.');
    }

    /**
     * Hapus kegiatan beserta berkas gambarnya.
     */
    public function destroy(Activity $activity): RedirectResponse
    {
        $this->authorize('delete', $activity);

        if ($activity->image !== null && $activity->image !== '') {
            Storage::disk('public')->delete($activity->image);
        }

        $activity->delete();

        return back()->with('success', 'Kegiatan berhasil dihapus.');
    }
}
