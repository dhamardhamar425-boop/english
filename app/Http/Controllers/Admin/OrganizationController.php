<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Models\Organization;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Pengelolaan profil & pengaturan organisasi oleh admin.
 *
 * Admin organisasi hanya boleh mengubah organisasinya sendiri
 * (dijamin oleh OrganizationPolicy + middleware org_admin).
 */
final class OrganizationController extends AdminController
{
    /**
     * Daftar semua organisasi (super admin) atau organisasi sendiri (admin organisasi).
     */
    public function index(): View
    {
        $this->authorize('viewAny', Organization::class);

        $organizations = Organization::query()
            ->withCount('registrations')
            ->orderBy('name')
            ->get();

        return view('admin.organizations.index', [
            'organizations' => $organizations,
        ]);
    }

    /**
     * Detail satu organisasi.
     */
    public function show(Organization $organization): View
    {
        $this->authorize('view', $organization);

        $organization->load(['registrations' => fn ($q) => $q->latest()->limit(20), 'organizationActivities']);

        return view('admin.organizations.show', [
            'organization' => $organization,
        ]);
    }

    /**
     * Simpan perubahan pengaturan organisasi.
     */
    public function update(Request $request, Organization $organization): RedirectResponse
    {
        $this->authorize('update', $organization);

        $validated = $request->validate([
            'whatsapp_group_link' => ['nullable', 'url', 'max:255'],
            'contact_link' => ['nullable', 'url', 'max:255'],
            'vision' => ['nullable', 'string', 'max:4000'],
            'mission' => ['nullable', 'string', 'max:4000'],
            'is_active' => ['boolean'],
        ], [
            'whatsapp_group_link.url' => 'Tautan grup WhatsApp harus berupa URL yang valid (https://…).',
            'contact_link.url' => 'Tautan kontak harus berupa URL yang valid (https://…).',
        ]);

        $organization->update([
            'whatsapp_group_link' => $validated['whatsapp_group_link'] ?? null,
            'contact_link' => $validated['contact_link'] ?? null,
            'vision' => $validated['vision'] ?? null,
            'mission' => $validated['mission'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', "Pengaturan organisasi {$organization->name} berhasil diperbarui.");
    }
}
