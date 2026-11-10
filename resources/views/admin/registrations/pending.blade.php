@extends('layouts.admin')

@section('title', 'Perlu Ditinjau')
@section('subtitle', 'Pendaftar dengan status menunggu tinjauan admin')

@section('content')
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-6 py-3">No. Pendaftaran</th>
                        <th class="px-6 py-3">Siswa</th>
                        <th class="px-6 py-3">Organisasi</th>
                        <th class="px-6 py-3">Motivasi</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($registrations as $reg)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-6 py-3 font-mono text-xs font-semibold text-emerald-800">{{ $reg->registration_number }}</td>
                            <td class="px-6 py-3">
                                <p class="font-semibold text-slate-800">{{ $reg->full_name }}</p>
                                <p class="text-xs text-slate-500">{{ $reg->class }} · {{ $reg->created_at?->format('d/m/Y') }}</p>
                            </td>
                            <td class="px-6 py-3">
                                <span class="inline-flex items-center gap-1.5 text-xs font-semibold" style="color: {{ $reg->organization?->color }}">
                                    <span class="h-2 w-2 rounded-full" style="background-color: {{ $reg->organization?->color }}"></span>
                                    {{ $reg->organization?->name }}
                                </span>
                            </td>
                            <td class="max-w-xs px-6 py-3">
                                <p class="truncate text-xs text-slate-500">{{ \Illuminate\Support\Str::limit($reg->reason, 80) }}</p>
                            </td>
                            <td class="px-6 py-3 text-right">
                                <a href="{{ route('admin.registrations.show', $reg) }}"
                                   class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-900 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-emerald-800">
                                    Tinjau
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <x-icon name="clipboard-check" class="mx-auto h-10 w-10 text-success-300" />
                                <p class="mt-2 text-sm font-semibold text-slate-500">Semua pendaftar sudah ditinjau 🎉</p>
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
@endsection
