<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\Setting;
use Illuminate\View\View;

class OrganizationController extends Controller
{
    /**
     * Tampilkan daftar seluruh organisasi siswa.
     */
    public function index(): View
    {
        return view('organizations.index', [
            'organizations' => Organization::active()->orderBy('id')->get(),
            'registrationOpen' => Setting::isRegistrationOpen(),
        ]);
    }

    /**
     * Tampilkan halaman detail satu organisasi.
     */
    public function show(Organization $organization): View
    {
        abort_unless($organization->is_active, 404);

        $organization->load([
            'organizationActivities' => fn ($query) => $query->orderByDesc('occurred_at')->orderByDesc('id'),
            'galleries' => fn ($query) => $query->latest('id'),
        ]);

        return view('organizations.show', [
            'organization' => $organization,
            'registrationOpen' => Setting::isRegistrationOpen(),
        ]);
    }
}
