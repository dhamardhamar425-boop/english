<?php

namespace App\Enums;

enum UserRole: string
{
    case SuperAdmin = 'super_admin';
    case OrgAdmin = 'org_admin';

    /**
     * Label tampilan untuk ditampilkan di view.
     */
    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Admin',
            self::OrgAdmin => 'Admin Organisasi',
        };
    }
}
