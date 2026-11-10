<?php

namespace App\Policies;

use App\Models\Registration;
use App\Models\User;

class RegistrationPolicy
{
    /**
     * Semua admin terautentikasi dapat melihat daftar pendaftar,
     * namun cakupan datanya diatur oleh middleware dan controller.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Super Admin dapat melihat semua pendaftar; Admin Organisasi hanya
     * pendaftar pada organisasi yang menjadi tanggung jawabnya.
     */
    public function view(User $user, Registration $registration): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->organization_id !== null
            && $user->organization_id === $registration->organization_id;
    }

    /**
     * Perubahan status dan hasil akhir seleksi mengikuti aturan yang sama
     * dengan melihat pendaftar.
     */
    public function update(User $user, Registration $registration): bool
    {
        return $this->view($user, $registration);
    }
}
