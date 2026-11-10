<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Organization;

/**
 * Dasar untuk seluruh controller dashboard admin.
 *
 * Menyediakan helper untuk organisasi yang sedang aktif dipilih admin
 * organisasi (scope organisasi dipaksakan lewat middleware org_admin).
 */
abstract class AdminController extends Controller
{
    /**
     * Mengambil organisasi aktif untuk admin organisasi, atau null untuk super admin.
     */
    protected function activeOrganization(): ?Organization
    {
        $user = auth()->user();

        if ($user === null || $user->organization_id === null) {
            return null;
        }

        return Organization::find($user->organization_id);
    }

    /**
     * Menentukan apakah pengguna yang login adalah super admin.
     */
    protected function isSuperAdmin(): bool
    {
        return auth()->user()?->role === UserRole::SuperAdmin;
    }
}
