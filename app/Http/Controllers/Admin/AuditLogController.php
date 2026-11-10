<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Models\AuditLog;
use Illuminate\Contracts\View\View;

/**
 * Menampilkan log aktivitas admin (audit trail) oleh Super Admin.
 */
final class AuditLogController extends AdminController
{
    public function index(): View
    {
        return view('admin.audit-logs.index', [
            'logs' => AuditLog::query()
                ->with('user')
                ->latest()
                ->paginate(30),
        ]);
    }
}
