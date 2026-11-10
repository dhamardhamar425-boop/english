@extends('layouts.admin')

@section('title', 'Log Aktivitas')
@section('subtitle', 'Riwayat kegiatan & aktivitas organisasi siswa')

@section('content')
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-6 py-3">Waktu</th>
                        <th class="px-6 py-3">Organisasi</th>
                        <th class="px-6 py-3">Judul Kegiatan</th>
                        <th class="px-6 py-3">Deskripsi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($activities as $activity)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-6 py-3 text-xs text-slate-600">{{ $activity->occurred_at?->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-3 text-sm text-slate-700">{{ $activity->organization?->name ?? '—' }}</td>
                            <td class="px-6 py-3 font-semibold text-slate-800">{{ $activity->title }}</td>
                            <td class="px-6 py-3 text-slate-600 line-clamp-2">{{ $activity->description }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <x-icon name="calendar" class="mx-auto h-10 w-10 text-slate-300" />
                                <p class="mt-2 text-sm font-semibold text-slate-500">Belum ada kegiatan tercatat.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-100 px-6 py-4">
            {{ $activities->links() }}
        </div>
    </div>
@endsection
