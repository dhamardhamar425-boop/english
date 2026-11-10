
@extends('layouts.public')

@section('title', 'Pengumuman Hasil Seleksi')

@section('fullscreen', 'true')

@section('content')
    @php
        $statusKey = strtolower(trim((string) $visibleStatus));

        $isAccepted = in_array($statusKey, [
            'diterima', 'lulus', 'lolos', 'accepted', 'passed'
        ], true);

        $isRejected = in_array($statusKey, [
            'tidak_diterima', 'tidak_lulus', 'tidak_lolos',
            'ditolak', 'rejected', 'failed'
        ], true);

        $isWaiting = !$isAccepted && !$isRejected;

        $scheduledAt = $resultAnnouncement?->scheduledAt();
    @endphp

    {{-- ======================================================
         HEADER PORTAL
    ======================================================= --}}
   
    {{-- ======================================================
         KONTEN UTAMA
    ======================================================= --}}
    <main class="min-h-screen w-full bg-[#f3f6fa] px-4 py-9 sm:px-6 lg:px-10 xl:px-14">

        <div class="mx-auto w-full">

            {{-- Breadcrumb --}}
            <div class="mb-6 flex flex-wrap items-center gap-2 text-xs text-slate-500">
                <a href="{{ route('home') }}" class="transition hover:text-blue-700">
                    Beranda
                </a>
                <x-icon name="chevron-right" class="h-3 w-3" />
                <a href="{{ route('status.check') }}" class="transition hover:text-blue-700">
                    Cek Pendaftaran
                </a>
                <x-icon name="chevron-right" class="h-3 w-3" />
                <span class="font-semibold text-blue-900">
                    Hasil Seleksi
                </span>
            </div>

<!-- 
            {{-- ==================================================
                 KARTU PENGUMUMAN
            =================================================== --}}
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl shadow-slate-900/5">

                {{-- Identitas portal --}}
                <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-200 px-5 py-4 sm:px-8 lg:px-12">
                    <div>
                        <p class="text-sm font-black text-blue-950">
                            PENGUMUMAN HASIL SELEKSI
                        </p>
                        <p class="mt-1 text-[10px] font-semibold uppercase tracking-wider text-slate-400">
                            Informasi hasil seleksi peserta
                        </p>
                    </div>

                    <div class="flex items-center gap-2">
                        @if ($registration->organization->logoUrl())
                            <img src="{{ $registration->organization->logoUrl() }}"
                                alt="Logo {{ $registration->organization->name }}"
                                class="h-10 w-10 object-contain">
                        @else
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50 text-xs font-black text-blue-900">
                                {{ $registration->organization->initials() }}
                            </div>
                        @endif

                        <div class="hidden text-right sm:block">
                            <p class="text-[10px] font-bold text-slate-700">
                                {{ $registration->organization->name }}
                            </p>
                            <p class="text-[9px] text-slate-400">
                                Portal Seleksi
                            </p>
                        </div>
                    </div>
                </div>
 -->

                {{-- Banner hasil --}}
                @if ($isAccepted)
                    <div class="relative overflow-hidden bg-gradient-to-r from-[#123c72] via-[#0756a0] to-[#0879c9] px-5 py-7 text-white sm:px-9 sm:py-9 lg:px-12">
                        <div class="absolute -right-12 -top-20 h-64 w-64 rounded-full border-[30px] border-white/5"></div>

                        <div class="relative flex items-start gap-4">
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-white/15 ring-1 ring-white/30">
                                <x-icon name="check-circle" class="h-6 w-6" />
                            </div>

                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-blue-100">
                                    Hasil Seleksi
                                </p>

                                <h2 class="mt-2 text-xl font-black leading-snug sm:text-2xl">
                                    SELAMAT! ANDA DINYATAKAN LULUS SELEKSI
                                </h2>

                                <p class="mt-2 text-xs leading-relaxed text-blue-100">
                                    Anda telah dinyatakan diterima dalam proses seleksi.
                                    Selamat atas pencapaian Anda!
                                </p>
                            </div>
                        </div>
                    </div>

                @elseif ($isRejected)
                    <div class="relative overflow-hidden bg-gradient-to-r from-[#a71925] via-[#dc2626] to-[#ef4444] px-5 py-7 text-white sm:px-9 sm:py-9 lg:px-12">
                        <div class="absolute -right-12 -top-20 h-64 w-64 rounded-full border-[30px] border-white/5"></div>

                        <div class="relative flex items-start gap-4">
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-white/15 ring-1 ring-white/30">
                                <x-icon name="info" class="h-6 w-6" />
                            </div>

                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-red-100">
                                    Hasil Seleksi
                                </p>

                                <h2 class="mt-2 text-xl font-black leading-snug sm:text-2xl">
                                    MOHON MAAF, ANDA BELUM DINYATAKAN LULUS SELEKSI
                                </h2>

                                <p class="mt-2 text-xs leading-relaxed text-red-100">
                                    Tetap semangat. Kesempatan untuk berkembang
                                    dan mencoba kembali masih terbuka.
                                </p>
                            </div>
                        </div>
                    </div>

                @else
                    <div class="relative overflow-hidden bg-gradient-to-r from-[#334155] to-[#475569] px-5 py-7 text-white sm:px-9 sm:py-9 lg:px-12">
                        <div class="relative flex items-start gap-4">
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-white/15">
                                <x-icon name="clock" class="h-6 w-6" />
                            </div>

                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-slate-200">
                                    Informasi Hasil Seleksi
                                </p>

                                <h2 class="mt-2 text-xl font-black leading-snug sm:text-2xl">
                                    HASIL SELEKSI BELUM DIUMUMKAN
                                </h2>

                                <p class="mt-2 text-xs leading-relaxed text-slate-200">
                                    Silakan menunggu sampai pengumuman resmi dibuka.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif


                {{-- ==================================================
                     AREA IDENTITAS PESERTA
                =================================================== --}}
                <div class="relative overflow-hidden bg-[#171a20] px-5 py-8 text-white sm:px-9 sm:py-10 lg:px-12">

                    <div class="pointer-events-none absolute -right-24 -top-24 h-80 w-80 rounded-full border border-white/[0.04]"></div>
                    <div class="pointer-events-none absolute -right-12 -top-12 h-56 w-56 rounded-full border border-white/[0.04]"></div>

                    <div class="relative">

                        {{-- Nama --}}
                        <div class="mb-8">
                            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">
                                Nama Peserta
                            </p>

                            <h3 class="mt-2 break-words text-2xl font-black uppercase leading-tight tracking-wide text-white sm:text-4xl">
                                {{ $registration->full_name }}
                            </h3>

                            <div class="mt-3 inline-flex items-center gap-2 rounded-md border border-white/10 bg-white/5 px-3 py-2">
                                <x-icon name="credit-card" class="h-3.5 w-3.5 text-cyan-400" />
                                <span class="font-mono text-xs tracking-wider text-cyan-300">
                                    {{ $registration->registration_number }}
                                </span>
                            </div>
                        </div>

                        <div class="grid gap-x-10 gap-y-7 border-t border-white/10 pt-7 sm:grid-cols-2 lg:grid-cols-4">

                            <div>
                                <p class="text-[10px] font-medium text-slate-400">
                                    Tanggal Pendaftaran
                                </p>
                                <p class="mt-1.5 text-sm font-bold text-white">
                                    {{ $registration->registered_at->isoFormat('DD/MM/YYYY') }}
                                </p>
                            </div>

                            <div>
                                <p class="text-[10px] font-medium text-slate-400">
                                    Organisasi Pilihan
                                </p>
                                <p class="mt-1.5 text-sm font-bold text-white">
                                    {{ $registration->organization->name }}
                                </p>
                            </div>

                            <div>
                                <p class="text-[10px] font-medium text-slate-400">
                                    Kelas
                                </p>
                                <p class="mt-1.5 text-sm font-bold text-white">
                                    {{ $registration->class }}
                                </p>
                            </div>

                            <div>
                                <p class="text-[10px] font-medium text-slate-400">
                                    Status Pendaftaran
                                </p>
                                <p class="mt-1.5 text-sm font-black
                                    {{ $isAccepted ? 'text-emerald-400' : ($isRejected ? 'text-red-400' : 'text-amber-300') }}">
                                    {{ $isAccepted ? 'DITERIMA' : ($isRejected ? 'TIDAK DITERIMA' : 'MENUNGGU PENGUMUMAN') }}
                                </p>
                            </div>

                        </div>

                        {{-- Footer kartu gelap --}}
                        <div class="mt-9 flex flex-wrap items-end justify-between gap-4 border-t border-white/10 pt-5">
                            <div>
                                <p class="text-[9px] font-bold uppercase tracking-widest text-slate-500">
                                    Dokumen Informasi Seleksi
                                </p>
                                <p class="mt-1 text-[10px] text-slate-400">
                                    {{ $registration->organization->name }}
                                </p>
                            </div>

                            <div class="text-right">
                                <p class="text-[10px] font-bold text-slate-300">
                                    {{ now()->year }}
                                </p>
                                <p class="mt-0.5 text-[9px] tracking-widest text-slate-500">
                                    PORTAL SELEKSI
                                </p>
                            </div>
                        </div>

                    </div>
                </div>


                {{-- Footer kartu --}}
                <div class="flex flex-wrap items-center justify-between gap-3 bg-slate-100 px-5 py-3 sm:px-8 lg:px-12">
                    <p class="text-[10px] text-slate-500">
                        Informasi berdasarkan data pendaftaran peserta.
                    </p>

                    <div class="h-1.5 w-20 rounded-full bg-gradient-to-r from-red-600 to-blue-800"></div>
                </div>

            </div>


            {{-- ==================================================
                 INFORMASI TAMBAHAN
            =================================================== --}}
            @if ($isWaiting)
                <div class="mt-6 overflow-hidden rounded-xl border border-amber-200 bg-white shadow-sm">
                    <div class="h-1 bg-amber-500"></div>

                    <div class="flex items-start gap-4 p-5">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                            <x-icon name="hourglass" class="h-5 w-5" />
                        </div>

                        <div>
                            <h3 class="text-sm font-bold text-amber-950">
                                Menunggu Pengumuman Resmi
                            </h3>

                            <p class="mt-1 text-xs leading-relaxed text-slate-600">
                                Admin organisasi telah mengisi hasil akhir, tetapi hasil
                                belum dapat ditampilkan sebelum pengumuman resmi dibuka.

                                @if ($scheduledAt)
                                    Jadwal pengumuman:
                                    <strong class="text-amber-900">
                                        {{ $scheduledAt->isoFormat('D MMMM YYYY') }},
                                        pukul {{ $scheduledAt->format('H:i') }} WIB.
                                    </strong>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            @endif


            {{-- ==================================================
                 TIMELINE DAN JADWAL
            =================================================== --}}
            <div class="mt-8 grid gap-6 md:grid-cols-2">

                {{-- Indikator pengumuman --}}
                <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-100 px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-50 text-blue-800">
                                <x-icon name="calendar-days" class="h-5 w-5" />
                            </div>

                            <div>
                                <h3 class="text-sm font-bold text-slate-900">
                                    Jadwal Pengumuman
                                </h3>
                                <p class="text-[10px] text-slate-500">
                                    Informasi waktu seleksi
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="p-5">
                        <x-announcement-indicator
                            :state="$announcementState"
                            :announcement="$resultAnnouncement"
                        />
                    </div>
                </div>


                {{-- Timeline --}}
                <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-100 px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-red-50 text-red-700">
                                <x-icon name="history" class="h-5 w-5" />
                            </div>

                            <div>
                                <h3 class="text-sm font-bold text-slate-900">
                                    Timeline Seleksi
                                </h3>
                                <p class="text-[10px] text-slate-500">
                                    Riwayat proses pendaftaran
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="p-5">
                        <ol class="space-y-0">
                            @foreach ($timeline as $item)
                                <li class="relative flex gap-4 pb-6 last:pb-0">

                                    @if (! $loop->last)
                                        <span class="absolute left-[7px] top-5 h-full w-px bg-blue-100"></span>
                                    @endif

                                    <span class="relative mt-1 h-4 w-4 shrink-0 rounded-full ring-4
                                        {{ $loop->last ? 'bg-red-600 ring-red-100' : 'bg-blue-700 ring-blue-100' }}">
                                    </span>

                                    <div>
                                        <p class="text-xs font-bold text-slate-800">
                                            {{ $item['label'] }}
                                        </p>

                                        <p class="mt-1 text-[10px] text-slate-500">
                                            {{ $item['date']->isoFormat('D MMMM YYYY, H:i') }} WIB
                                        </p>
                                    </div>
                                </li>
                            @endforeach

                            @if ($isWaiting)
                                <li class="flex gap-4">
                                    <span class="mt-1 h-4 w-4 shrink-0 rounded-full bg-slate-300 ring-4 ring-slate-100"></span>

                                    <div>
                                        <p class="text-xs font-bold text-slate-500">
                                            Menunggu Pengumuman
                                        </p>
                                        <p class="mt-1 text-[10px] text-slate-400">
                                            Menunggu jadwal resmi
                                        </p>
                                    </div>
                                </li>
                            @endif
                        </ol>
                    </div>
                </div>

            </div>


            {{-- ==================================================
                 PRIVASI
            =================================================== --}}
            <div class="mt-6 flex items-start gap-3 rounded-xl border border-blue-100 bg-blue-50 p-4">
                <x-icon name="shield-check" class="mt-0.5 h-5 w-5 shrink-0 text-blue-800" />

                <div>
                    <p class="text-xs font-bold text-blue-950">
                        Keamanan dan Privasi Peserta
                    </p>
                    <p class="mt-1 text-[11px] leading-relaxed text-blue-800">
                        Data sensitif seperti NIK, nomor telepon, alamat, dan catatan
                        internal admin tidak ditampilkan pada halaman ini.
                    </p>
                </div>
            </div>


            {{-- ==================================================
                 TOMBOL NAVIGASI
            =================================================== --}}
            <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
                <a href="{{ route('status.check') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-6 py-3 text-sm font-bold text-slate-700 transition hover:border-blue-700 hover:bg-blue-50 hover:text-blue-900">
                    <x-icon name="search" class="h-4 w-4" />
                    Cek Pendaftaran Lain
                </a>

                <a href="{{ route('home') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#123c72] px-6 py-3 text-sm font-bold text-white shadow-lg shadow-blue-900/10 transition hover:bg-blue-800">
                    <x-icon name="home" class="h-4 w-4" />
                    Kembali ke Beranda
                </a>
            </div>


            {{-- Catatan --}}
            <p class="mt-8 text-center text-[10px] leading-relaxed text-slate-400">
                Pastikan seluruh informasi yang ditampilkan sudah sesuai dengan data pendaftaran Anda.
                <br>
                © {{ now()->year }} {{ config('app.name') }}. Seluruh hak dilindungi.
            </p>

        </div>
    </main>
@endsection
