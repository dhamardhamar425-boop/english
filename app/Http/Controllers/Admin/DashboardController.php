<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\AnnouncementType;
use App\Enums\RegistrationStatus;
use App\Enums\UserRole;
use App\Models\Announcement;
use App\Models\Organization;
use App\Models\Registration;
use App\Models\Setting;
use Illuminate\Contracts\View\View;

/**
 * Dashboard admin: ringkasan statistik pendaftaran & seleksi organisasi siswa.
 */
final class DashboardController extends AdminController
{
    public function __invoke(): View
    {
        $user = auth()->user();
        $isSuperAdmin = $user?->role === UserRole::SuperAdmin;

        $scope = $isSuperAdmin
            ? Registration::query()
            : Registration::query()->where('organization_id', $user?->organization_id);

        // Statistik pendaftaran hanya tersedia untuk Super Admin.
        // Admin organisasi tidak melihat ringkasan statistik organisasi di dashboardnya.
        if ($isSuperAdmin) {
            $total = (clone $scope)->count();

            $perStatus = [
                'menunggu' => (clone $scope)->where('status', RegistrationStatus::Menunggu)->count(),
                'seleksi' => (clone $scope)->where('status', RegistrationStatus::Seleksi)->count(),
                'diterima' => (clone $scope)->where('final_result', 'diterima')->count(),
                'ditolak' => (clone $scope)->where('final_result', 'ditolak')->count(),
            ];

            $organizations = Organization::query()
                ->withCount([
                    'registrations',
                    'registrations as menunggu_count' => fn ($q) => $q->where('status', RegistrationStatus::Menunggu),
                    'registrations as seleksi_count' => fn ($q) => $q->where('status', RegistrationStatus::Seleksi),
                    'registrations as diterima_count' => fn ($q) => $q->where('final_result', 'diterima'),
                    'registrations as ditolak_count' => fn ($q) => $q->where('final_result', 'ditolak'),
                ])
                ->orderBy('name')
                ->get();

            $chartData = $organizations->map(fn (Organization $org) => [
                'name' => $org->name,
                'color' => $org->color,
                'total' => $org->registrations_count,
            ])->values();

            $maxChart = max(1, (int) $chartData->max('total'));
        } else {
            $total = null;
            $perStatus = [];
            $organizations = collect();
            $chartData = collect();
            $maxChart = 1;
        }

        $latestRegistrations = (clone $scope)
            ->with(['organization'])
            ->latest()
            ->limit(8)
            ->get();

        $studentAccounts = null;

        return view('admin.dashboard', [
            'schoolName' => Setting::cached('school_name', config('app.name')),
            'isSuperAdmin' => $isSuperAdmin,
            'activeOrganization' => $this->activeOrganization(),
            'total' => $total,
            'perStatus' => $perStatus,
            'organizations' => $organizations,
            'latestRegistrations' => $latestRegistrations,
            'studentAccounts' => $studentAccounts,
            'chartData' => $chartData,
            'maxChart' => $maxChart,
            'upcomingAnnouncement' => Announcement::query()
                ->where('type', AnnouncementType::Result)
                ->orderBy('announcement_date')
                ->orderBy('announcement_time')
                ->first(),
        ]);
    }
}
