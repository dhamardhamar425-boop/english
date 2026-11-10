@extends('layouts.admin')

@section('title', 'Dashboard')
@section('subtitle', $isSuperAdmin ? 'Ringkasan seluruh pendaftaran organisasi siswa' : 'Ringkasan pendaftaran '.($activeOrganization?->name ?? 'organisasi'))

@section('content')
    <div class="space-y-6">
        {{-- Kartu statistik (hanya Super Admin) --}}
        @if ($isSuperAdmin)
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Pendaftar</p>
                <p class="mt-2 font-display text-3xl font-extrabold text-emerald-900">{{ $total }}</p>
                <p class="mt-1 text-xs text-slate-500">Seluruh organisasi</p>
            </div>
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5">
                <p class="text-xs font-semibold uppercase tracking-wider text-amber-700">Menunggu</p>
                <p class="mt-2 font-display text-3xl font-extrabold text-amber-600">{{ $perStatus['menunggu'] }}</p>
                <p class="mt-1 text-xs text-amber-700/80">Belum ditinjau</p>
            </div>
            <div class="rounded-2xl border border-blue-200 bg-blue-50 p-5">
                <p class="text-xs font-semibold uppercase tracking-wider text-blue-700">Proses Seleksi</p>
                <p class="mt-2 font-display text-3xl font-extrabold text-blue-600">{{ $perStatus['seleksi'] }}</p>
                <p class="mt-1 text-xs text-blue-700/80">Sedang dinilai</p>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="rounded-2xl border border-success-200 bg-success-50 p-5">
                    <p class="text-xs font-semibold uppercase tracking-wider text-success-700">Diterima</p>
                    <p class="mt-2 font-display text-3xl font-extrabold text-success-600">{{ $perStatus['diterima'] }}</p>
                </div>
                <div class="rounded-2xl border border-danger-200 bg-danger-50 p-5">
                    <p class="text-xs font-semibold uppercase tracking-wider text-danger-700">Ditolak</p>
                    <p class="mt-2 font-display text-3xl font-extrabold text-danger-600">{{ $perStatus['ditolak'] }}</p>
                </div>
            </div>
        </div>
        @endif

        <div class="grid gap-6 xl:grid-cols-3">
            {{-- Grafik batang per organisasi (hanya Super Admin) --}}
            @if ($isSuperAdmin)
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm xl:col-span-2">
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h2 class="font-display text-lg font-extrabold text-emerald-900">Pendaftar per Organisasi</h2>
                        <p class="text-xs text-slate-500">Perbandingan jumlah pendaftar antar organisasi</p>
                    </div>
                    <x-icon name="bar-chart-3" class="h-5 w-5 text-slate-400" />
                </div>

                <div class="space-y-4">
                    @forelse ($chartData as $bar)
                        <div>
                            <div class="mb-1.5 flex items-center justify-between text-sm">
                                <span class="font-semibold text-slate-700">{{ $bar['name'] }}</span>
                                <span class="font-bold text-emerald-900">{{ $bar['total'] }}</span>
                            </div>
                            <div class="h-3 w-full overflow-hidden rounded-full bg-slate-100">
                                <div class="h-full rounded-full transition-all"
                                     style="width: {{ max(3, (int) round($bar['total'] / $maxChart * 100)) }}%; background-color: {{ $bar['color'] }};"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">Belum ada data organisasi.</p>
                    @endforelse
                </div>
            </div>
            @endif

            {{-- Pengumuman & info --}}
            <div class="space-y-6 {{ $isSuperAdmin ? 'xl:col-span-1' : 'xl:col-span-3' }}">
                @if ($upcomingAnnouncement)
                    <div class="rounded-2xl border border-emerald-800 bg-emerald-900 p-6 text-white">
                        <div class="flex items-center gap-2 text-accent-300">
                            <x-icon name="megaphone" class="h-4 w-4" />
                            <p class="text-xs font-bold uppercase tracking-widest">Pengumuman Hasil</p>
                        </div>
                        <p class="mt-3 font-display text-xl font-extrabold">{{ $upcomingAnnouncement->title }}</p>
                        <p class="mt-1 text-sm text-slate-300">
                            Dijadwalkan {{ $upcomingAnnouncement->scheduled_at?->translatedFormat('d F Y') }}
                            pukul {{ $upcomingAnnouncement->scheduled_at?->format('H:i') }} WIB
                        </p>
                        <p class="mt-3 inline-flex rounded-full bg-white/10 px-3 py-1 text-xs font-semibold ring-1 ring-white/20">
                            {{ $upcomingAnnouncement->is_open ? 'Sedang Dibuka' : 'Belum Dibuka' }}
                        </p>
                    </div>
                @endif

                @if ($isSuperAdmin)
                    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h3 class="font-display text-base font-extrabold text-emerald-900">Akun Siswa Terdaftar</h3>
                        <p class="mt-2 font-display text-4xl font-extrabold text-emerald-900">{{ $studentAccounts ?? 0 }}</p>
                        <p class="mt-1 text-xs text-slate-500">Akun siswa yang telah mendaftar di sistem</p>
                        <a href="{{ route('admin.admins.index') }}" class="mt-4 inline-flex items-center gap-1.5 text-sm font-semibold text-primary-600 hover:text-primary-700">
                            Kelola akun admin
                            <x-icon name="chevron-right" class="h-4 w-4" />
                        </a>
                    </div>
                @endif
            </div>
        </div>

        {{-- Pendaftar terbaru --}}
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                <div>
                    <h2 class="font-display text-lg font-extrabold text-emerald-900">Pendaftar Terbaru</h2>
                    <p class="text-xs text-slate-500">8 pendaftaran terakhir yang masuk</p>
                </div>
                <a href="{{ route('admin.registrations.index') }}" class="text-sm font-semibold text-primary-600 hover:text-primary-700">Lihat semua</a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-6 py-3">No. Pendaftaran</th>
                            <th class="px-6 py-3">Siswa</th>
                            <th class="px-6 py-3">Organisasi</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($latestRegistrations as $reg)
                            <tr class="hover:bg-slate-50/70">
                                <td class="px-6 py-3 font-mono text-xs font-semibold text-emerald-800">{{ $reg->registration_number }}</td>
                                <td class="px-6 py-3">
                                    <p class="font-semibold text-slate-800">{{ $reg->full_name }}</p>
                                    <p class="text-xs text-slate-500">{{ $reg->class }}</p>
                                </td>
                                <td class="px-6 py-3">
                                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold" style="color: {{ $reg->organization?->color }}">
                                        <span class="h-2 w-2 rounded-full" style="background-color: {{ $reg->organization?->color }}"></span>
                                        {{ $reg->organization?->name }}
                                    </span>
                                </td>
                                <td class="px-6 py-3"><x-status-badge :status="$reg->status" /></td>
                                <td class="px-6 py-3 text-xs text-slate-500">{{ $reg->created_at?->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-slate-500">Belum ada pendaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
