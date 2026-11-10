<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOrgAdmin
{
    /**
     * Batasi akses rute hanya untuk Admin Organisasi dengan organisasi aktif.
     *
     * Admin organisasi hanya boleh mengakses data organisasi tanggung jawabnya
     * (scope query di controller). Rute yang juga boleh diakses Super Admin
     * tidak memakai middleware ini — cukup `auth` + scoping controller/policy.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user?->isOrgAdmin() || $user->organization_id === null) {
            abort(403, 'Akses ditolak. Halaman ini hanya untuk Admin Organisasi.');
        }

        return $next($request);
    }
}
