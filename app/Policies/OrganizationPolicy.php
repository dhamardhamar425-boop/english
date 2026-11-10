<?php

namespace App\Policies;

use App\Models\Organization;
use App\Models\User;

class OrganizationPolicy
{
    /**
     * Semua admin terautentikasi dapat melihat daftar organisasi.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Super Admin melihat semua organisasi; Admin Organisasi hanya
     * organisasi miliknya.
     */
    public function view(User $user, Organization $organization): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->organization_id !== null
            && $user->organization_id === $organization->id;
    }

    /**
     * Admin Organisasi diperbolehkan memperbarui informasi organisasinya
     * sendiri; penghapusan hanya untuk Super Admin.
     */
    public function update(User $user, Organization $organization): bool
    {
        return $this->view($user, $organization);
    }

    /**
     * Hanya Super Admin yang boleh menghapus organisasi.
     */
    public function delete(User $user, Organization $organization): bool
    {
        return $user->isSuperAdmin();
    }
}
