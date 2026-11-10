<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Requests\StoreGalleryRequest;
use App\Http\Requests\UpdateGalleryRequest;
use App\Models\Gallery;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

/**
 * Pengelolaan galeri foto organisasi oleh admin.
 *
 * Admin organisasi hanya boleh mengelola galeri organisasinya sendiri
 * (dijamin oleh GalleryPolicy + scoping di controller).
 */
final class GalleryController extends AdminController
{
    /**
     * Daftar foto galeri — seluruh organisasi (super admin) atau
     * organisasi sendiri (admin organisasi).
     */
    public function index(): View
    {
        $this->authorize('viewAny', Gallery::class);

        $organization = $this->activeOrganization();

        $galleries = Gallery::query()
            ->with('organization')
            ->when(
                $organization !== null,
                fn ($query) => $query->where('organization_id', $organization->id),
            )
            ->latest()
            ->paginate(24);

        return view('admin.galleries.index', [
            'galleries' => $galleries,
        ]);
    }

    /**
     * Simpan foto galeri baru.
     */
    public function store(StoreGalleryRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        Gallery::create([
            'organization_id' => $validated['organization_id'] ?? $this->activeOrganization()?->id,
            'title' => $validated['title'],
            'image' => $validated['image']->store('galleries', 'public'),
        ]);

        return back()->with('success', 'Foto galeri berhasil ditambahkan.');
    }

    /**
     * Perbarui judul foto atau ganti berkasnya.
     */
    public function update(UpdateGalleryRequest $request, Gallery $gallery): RedirectResponse
    {
        $validated = $request->validated();

        $data = ['title' => $validated['title']];

        if (isset($validated['image'])) {
            Storage::disk('public')->delete($gallery->image);
            $data['image'] = $validated['image']->store('galleries', 'public');
        }

        $gallery->update($data);

        return back()->with('success', 'Foto galeri berhasil diperbarui.');
    }

    /**
     * Hapus foto galeri beserta berkas gambarnya.
     */
    public function destroy(Gallery $gallery): RedirectResponse
    {
        $this->authorize('delete', $gallery);

        if ($gallery->image !== null && $gallery->image !== '') {
            Storage::disk('public')->delete($gallery->image);
        }

        $gallery->delete();

        return back()->with('success', 'Foto galeri berhasil dihapus.');
    }
}
