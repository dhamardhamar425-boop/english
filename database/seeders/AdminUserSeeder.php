<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Akun admin yang disediakan beserta email dan PIN (kata sandi) masing-masing.
     *
     * @var array<string, array{email: string, pin: string}>
     */
    private const ADMIN_ACCOUNTS = [
        'super_admin' => [
            'email' => 'smkmanusaajb@gmail.com',
            'pin' => '5555',
            'name' => 'Super Admin',
            'role' => UserRole::SuperAdmin,
            'slug' => null,
        ],
        'ipnu-ippnu' => [
            'email' => 'ipnuippnu@gmail.com',
            'pin' => '0000',
            'name' => 'Admin IPNU-IPPNU',
            'role' => UserRole::OrgAdmin,
            'slug' => 'ipnu-ippnu',
        ],
        'osis' => [
            'email' => 'osisajb@gmail.com',
            'pin' => '9999',
            'name' => 'Admin OSIS',
            'role' => UserRole::OrgAdmin,
            'slug' => 'osis',
        ],
        'pmr' => [
            'email' => 'pmrajb@gmail.com',
            'pin' => '8888',
            'name' => 'Admin PMR',
            'role' => UserRole::OrgAdmin,
            'slug' => 'pmr',
        ],
        'pks' => [
            'email' => 'pksajb@gmail.com',
            'pin' => '7777',
            'name' => 'Admin PKS',
            'role' => UserRole::OrgAdmin,
            'slug' => 'pks',
        ],
        'paskohamas' => [
            'email' => 'paskohamasajb@gmail.com',
            'pin' => '6666',
            'name' => 'Admin PASKOHAMAS',
            'role' => UserRole::OrgAdmin,
            'slug' => 'paskohamas',
        ],
    ];

    /**
     * Buat akun Super Admin dan lima akun Admin Organisasi.
     */
    public function run(): void
    {
        foreach (self::ADMIN_ACCOUNTS as $account) {
            $organization = null;

            if ($account['slug'] !== null) {
                $organization = Organization::where('slug', $account['slug'])->first();

                if ($organization === null) {
                    continue;
                }
            }

            User::updateOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'password' => Hash::make($account['pin']), // PIN di-hash sebagai kata sandi login.
                    'role' => $account['role'],
                    'organization_id' => $organization?->id,
                    'email_verified_at' => now(),
                ],
            );
        }
    }
}
