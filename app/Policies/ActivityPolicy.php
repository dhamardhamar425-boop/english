<?php

namespace App\Policies;

use App\Models\Activity;
use App\Models\User;

class ActivityPolicy
{
    /**
     * Semua admin terautentikasi dapat melihat daftar kegiatan.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Super Admin dapat mengelola semua kegiatan; Admin Organisasi hanya
     * kegiatan organisasinya sendiri (dijamin pula oleh scoping di controller).
     */
    public function view(User $user, Activity $activity): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->organization_id !== null
            && $user->organization_id === $activity->organization_id;
    }

    /**
     * Aturan perubahan dan penghapusan mengikuti aturan yang sama dengan
     * melihat kegiatan.
     */
    public function update(User $user, Activity $activity): bool
    {
        return $this->view($user, $activity);
    }

    public function delete(User $user, Activity $activity): bool
    {
        return $this->view($user, $activity);
    }
}
