<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    /**
     * Hancurkan sesi admin dan keluar dari dashboard.
     */
    public function destroy(Request $request): RedirectResponse
    {
        AuditLog::record('auth.logout', Auth::user()?->name.' keluar dari dashboard admin.');

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
