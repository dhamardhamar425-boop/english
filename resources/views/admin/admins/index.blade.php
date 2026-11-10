@extends('layouts.admin')

@section('title', 'Akun Admin')
@section('subtitle', 'Kelola akun Super Admin & Admin Organisasi')

@section('content')
    <div class="grid gap-6 xl:grid-cols-3">
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm xl:col-span-2">
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-6 py-3">Nama</th>
                            <th class="px-6 py-3">Email</th>
                            <th class="px-6 py-3">Peran</th>
                            <th class="px-6 py-3">Organisasi</th>
                            <th class="px-6 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($admins as $admin)
                            <tr class="hover:bg-slate-50/70">
                                <td class="px-6 py-3 font-semibold text-slate-800">{{ $admin->name }}</td>
                                <td class="px-6 py-3 text-slate-600">{{ $admin->email }}</td>
                                <td class="px-6 py-3">
                                    <span @class([
                                        'rounded-full px-2.5 py-1 text-[11px] font-bold',
                                        'bg-emerald-100 text-emerald-700' => $admin->isSuperAdmin(),
                                        'bg-primary-100 text-primary-700' => $admin->isOrgAdmin(),
                                    ])>
                                        {{ $admin->isSuperAdmin() ? 'Super Admin' : 'Admin Organisasi' }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-slate-600">{{ $admin->organization?->name ?? '—' }}</td>
                                <td class="px-6 py-3 text-right">
                                    <button type="button"
                                            class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-primary-600 transition hover:bg-primary-50"
                                            onclick="document.getElementById('edit-admin-{{ $admin->id }}').classList.toggle('hidden')">
                                        <x-icon name="pencil" class="h-3.5 w-3.5" />
                                        Edit
                                    </button>
                                    <form method="POST" action="{{ route('admin.admins.destroy', $admin) }}" class="inline"
                                          onsubmit="return confirm('Yakin ingin menghapus akun {{ $admin->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center gap-1.5 rounded-lg border border-danger-200 px-3 py-1.5 text-xs font-semibold text-danger-600 transition hover:bg-danger-50">
                                            <x-icon name="trash" class="h-3.5 w-3.5" />
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <tr id="edit-admin-{{ $admin->id }}" class="hidden bg-slate-50/50">
                                <td colspan="5" class="px-6 py-4">
                                    <form method="POST" action="{{ route('admin.admins.update', $admin) }}" class="grid gap-3 md:grid-cols-5">
                                        @csrf
                                        @method('PUT')
                                        <input type="text" name="name" value="{{ old('name', $admin->name) }}" placeholder="Nama"
                                               class="rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500">
                                        <input type="email" name="email" value="{{ old('email', $admin->email) }}" placeholder="Email"
                                               class="rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500">
                                        <input type="password" name="password" placeholder="Kata sandi baru (opsional)"
                                               class="rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500">
                                        <select name="role" class="rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500">
                                            <option value="super_admin" @selected($admin->role->value === 'super_admin')>Super Admin</option>
                                            <option value="org_admin" @selected($admin->role->value === 'org_admin')>Admin Organisasi</option>
                                        </select>
                                        <select name="organization_id" class="rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500">
                                            <option value="">— Tanpa organisasi —</option>
                                            @foreach ($organizations as $org)
                                                <option value="{{ $org->id }}" @selected($admin->organization_id === $org->id)>{{ $org->name }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit"
                                                class="rounded-xl bg-emerald-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800 md:col-span-5">
                                            Simpan Perubahan
                                        </button>
                                    </form>
                                    @if ($errors->any() && old('_edit_id') == $admin->id)
                                        <ul class="mt-2 text-xs text-danger-600">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-slate-500">Belum ada akun admin.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Form tambah admin --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="font-display text-lg font-extrabold text-emerald-900">Tambah Akun Admin</h3>

            <form method="POST" action="{{ route('admin.admins.store') }}" class="mt-5 space-y-4">
                @csrf
                <div>
                    <label for="name" class="mb-1 block text-xs font-semibold text-slate-600">Nama Lengkap</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           class="w-full rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500">
                    @error('name') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="mb-1 block text-xs font-semibold text-slate-600">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                           class="w-full rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500">
                    @error('email') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="password" class="mb-1 block text-xs font-semibold text-slate-600">Kata Sandi</label>
                    <input type="password" name="password" id="password" required
                           class="w-full rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500">
                    @error('password') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="role" class="mb-1 block text-xs font-semibold text-slate-600">Peran</label>
                    <select name="role" id="role" class="w-full rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500">
                        <option value="super_admin" @selected(old('role') === 'super_admin')>Super Admin</option>
                        <option value="org_admin" @selected(old('role') === 'org_admin')>Admin Organisasi</option>
                    </select>
                    @error('role') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="organization_id" class="mb-1 block text-xs font-semibold text-slate-600">Organisasi (wajib untuk Admin Organisasi)</label>
                    <select name="organization_id" id="organization_id" class="w-full rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500">
                        <option value="">— Tanpa organisasi —</option>
                        @foreach ($organizations as $org)
                            <option value="{{ $org->id }}" @selected(old('organization_id') == $org->id)>{{ $org->name }}</option>
                        @endforeach
                    </select>
                    @error('organization_id') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                </div>

                <button type="submit"
                        class="inline-flex w-full items-center justify-center gap-1.5 rounded-xl bg-emerald-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-800">
                    <x-icon name="user-plus" class="h-4 w-4" />
                    Buat Akun Admin
                </button>
            </form>
        </div>
    </div>
@endsection
