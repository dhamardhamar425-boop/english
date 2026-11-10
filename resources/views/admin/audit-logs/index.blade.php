@extends('layouts.admin')

@section('title', 'Log Aktivitas')
@section('subtitle', 'Riwayat tindakan admin yang tercatat di sistem')

@section('content')
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-6 py-3">Waktu</th>
                        <th class="px-6 py-3">Admin</th>
                        <th class="px-6 py-3">Aksi</th>
                        <th class="px-6 py-3">Deskripsi</th>
                        <th class="px-6 py-3">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($logs as $log)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-6 py-3 text-xs text-slate-600">{{ $log->created_at?->format('d/m/Y H:i:s') }}</td>
                            <td class="px-6 py-3">
                                <p class="font-semibold text-slate-800">{{ $log->user?->name ?? 'Sistem' }}</p>
                                <p class="text-xs text-slate-500">{{ $log->user?->email }}</p>
                            </td>
                            <td class="px-6 py-3">
                                <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-[11px] font-bold text-emerald-700">{{ $log->action }}</span>
                            </td>
                            <td class="px-6 py-3 text-slate-600">{{ $log->description }}</td>
                            <td class="px-6 py-3 font-mono text-xs text-slate-500">{{ $log->ip_address }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <x-icon name="history" class="mx-auto h-10 w-10 text-slate-300" />
                                <p class="mt-2 text-sm font-semibold text-slate-500">Belum ada aktivitas tercatat.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-100 px-6 py-4">
            {{ $logs->links() }}
        </div>
    </div>
@endsection
