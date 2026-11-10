<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Pengelolaan akun admin oleh Super Admin.
 *
 * Hanya Super Admin yang boleh membuat/mengubah/menghapus akun admin.
 * Admin organisasi wajib terhubung ke satu organisasi aktif.
 */
final class AdminUserController extends AdminController
{
    /**
     * Daftar seluruh akun admin beserta organisasi yang ditugaskan.
     */
    public function index(): View
    {
        $admins = User::query()
            ->whereIn('role', [UserRole::SuperAdmin, UserRole::OrgAdmin])
            ->with('organization')
            ->orderByDesc('role')
            ->orderBy('name')
            ->get();

        return view('admin.admins.index', [
            'admins' => $admins,
            'organizations' => Organization::orderBy('name')->get(),
        ]);
    }

    /**
     * Simpan akun admin baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', Rule::enum(UserRole::class)],
            'organization_id' => ['required_if:role,org_admin', 'nullable', 'exists:organizations,id'],
        ], [
            'name.required' => 'Nama admin wajib diisi.',
            'email.required' => 'Email admin wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'role.required' => 'Peran admin wajib dipilih.',
            'organization_id.required_if' => 'Admin organisasi harus memiliki organisasi yang ditugaskan.',
            'organization_id.exists' => 'Organisasi tidak ditemukan.',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => UserRole::from($validated['role']),
            'organization_id' => $validated['organization_id'] ?? null,
            'email_verified_at' => now(),
        ]);

        return back()->with('success', "Akun admin {$validated['name']} berhasil dibuat.");
    }

    /**
     * Perbarui akun admin.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8'],
            'role' => ['required', Rule::enum(UserRole::class)],
            'organization_id' => ['required_if:role,org_admin', 'nullable', 'exists:organizations,id'],
        ], [
            'name.required' => 'Nama admin wajib diisi.',
            'email.required' => 'Email admin wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'role.required' => 'Peran admin wajib dipilih.',
            'organization_id.required_if' => 'Admin organisasi harus memiliki organisasi yang ditugaskan.',
            'organization_id.exists' => 'Organisasi tidak ditemukan.',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => UserRole::from($validated['role']),
            'organization_id' => $validated['organization_id'] ?? null,
        ]);

        if (! empty($validated['password'])) {
            $user->update(['password' => $validated['password']]);
        }

        return back()->with('success', "Akun admin {$validated['name']} berhasil diperbarui.");
    }

    /**
     * Hapus akun admin.
     */
    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun yang sedang digunakan.');
        }

        $user->delete();

        return back()->with('success', "Akun admin {$user->name} berhasil dihapus.");
    }
}
