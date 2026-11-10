@extends('layouts.public')

@section('title', 'Cek Status Pendaftaran')

@section('content')
    <section class="border-b border-slate-200 bg-emerald-50">
        <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 md:py-16 lg:px-8">
            <x-section-heading title="Cek Status Pendaftaran"
                subtitle="Masukkan nomor pendaftaran atau NIS Anda untuk melihat perkembangan pendaftaran dan hasil seleksi." />
        </div>
    </section>

    <section class="mx-auto grid max-w-6xl gap-8 px-4 py-14 sm:px-6 md:py-16 lg:grid-cols-2 lg:px-8">
        <div class="reveal">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm md:p-8">
                <h2 class="text-lg font-semibold text-emerald-800">Cari Data Pendaftaran</h2>
                <p class="mt-1 text-sm text-slate-600">
                    Contoh nomor pendaftaran: <span class="font-mono font-semibold text-emerald-700">OSIS-2026-0001</span>
                </p>

                <form method="POST" action="{{ route('status.store') }}" class="mt-6 space-y-4">
                    @csrf
                    <div>
                        <label for="identifier" class="mb-1.5 block text-sm font-medium text-slate-700">Nomor
                            Pendaftaran / NIS</label>
                        <input id="identifier" type="text" name="identifier"
                            value="{{ old('identifier') }}" required maxlength="30"
                            placeholder="OSIS-2026-0001 atau 102435"
                            class="w-full rounded-xl border border-slate-300 px-4 py-2.5 font-mono text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20" />
                        @error('identifier')
                            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-800 px-6 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500/40">
                        Cari Data
                        <x-icon name="search" class="h-4 w-4" />
                    </button>
                </form>

                <div class="mt-6 rounded-xl border border-blue-100 bg-blue-50 p-4 text-xs leading-relaxed text-blue-900">
                    <div class="flex items-start gap-2.5">
                        <x-icon name="lock" class="mt-0.5 h-4 w-4 shrink-0" />
                        <p>
                            Untuk keamanan data, pencarian dibatasi beberapa kali per menit. NIK tidak pernah
                            ditampilkan pada halaman status. Hasil akhir seleksi hanya tampil setelah pengumuman resmi
                            dibuka.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <aside class="reveal">
            <x-announcement-indicator :state="$announcementState" :announcement="$resultAnnouncement" />

            <div class="mt-6 space-y-4">
                <div class="rounded-2xl border border-slate-200 bg-white p-5">
                    <p class="text-sm font-semibold text-emerald-800">Yang ditampilkan</p>
                    <ul class="mt-3 space-y-2 text-sm text-slate-600">
                        <li class="flex items-center gap-2">
                            <x-icon name="check" class="h-4 w-4 text-emerald-600" /> Nama, nomor pendaftaran, dan kelas
                        </li>
                        <li class="flex items-center gap-2">
                            <x-icon name="check" class="h-4 w-4 text-emerald-600" /> Organisasi pilihan dan tanggal
                            pendaftaran
                        </li>
                        <li class="flex items-center gap-2">
                            <x-icon name="check" class="h-4 w-4 text-emerald-600" /> Status pendaftaran dan jadwal
                            pengumuman
                        </li>
                        <li class="flex items-center gap-2">
                            <x-icon name="check" class="h-4 w-4 text-emerald-600" /> Timeline proses seleksi
                        </li>
                    </ul>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5">
                    <p class="text-sm font-semibold text-emerald-800">Tidak ditampilkan</p>
                    <ul class="mt-3 space-y-2 text-sm text-slate-600">
                        <li class="flex items-center gap-2">
                            <x-icon name="x" class="h-4 w-4 text-red-500" /> NIK dan data pribadi sensitif
                        </li>
                        <li class="flex items-center gap-2">
                            <x-icon name="x" class="h-4 w-4 text-red-500" /> Catatan internal admin organisasi
                        </li>
                        <li class="flex items-center gap-2">
                            <x-icon name="x" class="h-4 w-4 text-red-500" /> Hasil akhir sebelum pengumuman dibuka
                        </li>
                    </ul>
                </div>
            </div>
        </aside>
    </section>
@endsection
