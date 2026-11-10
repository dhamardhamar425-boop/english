@extends('layouts.admin')

@section('title', 'Data Pendaftar')
@section('subtitle', 'Seluruh pendaftaran yang masuk ke sistem')

@section('content')
    <div class="space-y-4">
        <form method="GET" action="{{ route('admin.registrations.index') }}"
              class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="grid gap-3 md:grid-cols-4">
                <div class="md:col-span-2">
                    <label for="q" class="mb-1 block text-xs font-semibold text-slate-600">Pencarian</label>
                    <input type="text" name="q" id="q" value="{{ $filters['q'] ?? '' }}"
                           placeholder="Nama, NIS, atau nomor pendaftaran…"
                           class="w-full rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500">
                </div>

                @if ($isSuperAdmin)
                    <div>
                        <label for="organization" class="mb-1 block text-xs font-semibold text-slate-600">Organisasi</label>
                        <select name="organization" id="organization" class="w-full rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500">
                            <option value="">Semua</option>
                            @foreach ($organizations as $org)
                                <option value="{{ $org->id }}" @selected(($filters['organization'] ?? null) == $org->id)>{{ $org->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @else
                    <div></div>
                @endif

                <div>
                    <label for="status" class="mb-1 block text-xs font-semibold text-slate-600">Status</label>
                    <select name="status" id="status" class="w-full rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500">
                        <option value="">Semua</option>
                        @foreach (\App\Enums\RegistrationStatus::cases() as $st)
                            <option value="{{ $st->value }}" @selected(($filters['status'] ?? null) === $st->value)>{{ $st->label() }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-3 flex gap-2">
                <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">
                    <x-icon name="search" class="h-4 w-4" />
                    Terapkan Filter
                </button>
                <a href="{{ route('admin.registrations.index') }}" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                    Reset
                </a>
            </div>
        </form>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-6 py-3">No. Pendaftaran</th>
                            <th class="px-6 py-3">Siswa</th>
                            <th class="px-6 py-3">Kelas</th>
                            <th class="px-6 py-3">Organisasi</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Tanggal Daftar</th>
                            <th class="px-6 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($registrations as $reg)
                            <tr class="hover:bg-slate-50/70">
                                <td class="px-6 py-3 font-mono text-xs font-semibold text-emerald-800">{{ $reg->registration_number }}</td>
                                <td class="px-6 py-3 font-semibold text-slate-800">{{ $reg->full_name }}</td>
                                <td class="px-6 py-3 text-slate-600">{{ $reg->class }}</td>
                                <td class="px-6 py-3">
                                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold" style="color: {{ $reg->organization?->color }}">
                                        <span class="h-2 w-2 rounded-full" style="background-color: {{ $reg->organization?->color }}"></span>
                                        {{ $reg->organization?->name }}
                                    </span>
                                </td>
                                <td class="px-6 py-3"><x-status-badge :status="$reg->status" /></td>
                                <td class="px-6 py-3 text-xs text-slate-500">{{ $reg->created_at?->format('d/m/Y H:i') }}</td>
                                <td class="px-6 py-3 text-right">
                                    <a href="{{ route('admin.registrations.show', $reg) }}"
                                       class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-primary-600 transition hover:bg-primary-50">
                                        Detail
                                        <x-icon name="chevron-right" class="h-3.5 w-3.5" />
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <x-icon name="users" class="mx-auto h-10 w-10 text-slate-300" />
                                    <p class="mt-2 text-sm font-semibold text-slate-500">Belum ada data pendaftar</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-100 px-6 py-4">
                {{ $registrations->links() }}
            </div>
        </div>
    </div>
@endsection
