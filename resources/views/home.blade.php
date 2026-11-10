@extends('layouts.public')

@section('title', 'Beranda')

@section('content')
    {{-- Hero --}}
    <section class="relative overflow-hidden bg-emerald-900 text-white">
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute -left-40 -top-40 h-96 w-96 rounded-full bg-emerald-500/15 blur-3xl"></div>
            <div class="absolute -bottom-48 right-0 h-[32rem] w-[32rem] rounded-full bg-emerald-900/25 blur-3xl"></div>
                <div class="absolute top-1/4 right-10 opacity-20">
                    <svg class="h-12 w-12 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin=">
                        <path d="M12 2s6 6 8 12c0 0-2 6-8 12s-8-6-8-12c0 0 6-6 8-12Z" />
                    </svg>
                </div>
            <div class="absolute inset-0 bg-[linear-gradient(rgba(255,255,255,0.04)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,0.04)_1px,transparent_1px)] bg-[size:48px_48px] [mask-image:radial-gradient(ellipse_at_center,black,transparent_75%)]">
            </div>
        </div>

        <div class="relative mx-auto max-w-7xl px-4 py-20 sm:px-6 md:py-28 lg:px-8">
            <div class="max-w-3xl">
                <span
                    class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-1.5 text-xs font-medium text-emerald-200 ring-1 ring-white/15">
                    <x-icon name="megaphone" class="h-3.5 w-3.5" />
                    Portal Resmi Pendaftaran Organisasi Siswa
                </span>
                <h1 class="mt-6 text-3xl font-semibold leading-tight tracking-tight sm:text-4xl md:text-5xl">
                    TEMUKAN POTENSIMU,<br class="hidden sm:block" />
                    <span class="text-emerald-300">KEMBANGKAN DIRIMU!</span>
                </h1>
                <p class="mt-5 max-w-2xl text-sm leading-relaxed text-emerald-200 sm:text-base">
                    Bergabunglah dengan organisasi sekolah dan temukan pengalaman, teman, serta kemampuan baru
                    bersama kami.
                </p>
            </div>
        </div>
    </section>

    {{-- Status periode pendaftaran --}}
    <div class="mx-auto max-w-7xl px-4 pt-8 sm:px-6 lg:px-8">
        <x-period-banner />
    </div>

    {{-- Pengenalan organisasi --}}
    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 md:py-20 lg:px-8">
        <div class="flex flex-col items-start justify-between gap-4 md:flex-row md:items-end">
            <x-section-heading align="left" title="Kenali Organisasi Kami"
                subtitle="Lima organisasi siswa siap menumbuhkan kepemimpinan, kreativitas, dan kepedulian Anda."
                class="max-w-2xl text-left" />
            </div>

            <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($organizations as $organization)
                <div class="reveal">
                    <x-org-card :organization="$organization" :registration-open="$registrationOpen" />
                </div>
            @endforeach
        </div>
    </section>

    {{-- Alur pendaftaran --}}
    <section class="border-y border-slate-200 bg-slate-50">
        <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 md:py-20 lg:px-8">
            <x-section-heading title="Alur Pendaftaran"
                subtitle="Enam langkah sederhana dari memilih organisasi hingga mengetahui hasil seleksi." />

            <div class="mt-10">
                <x-flow-steps />
            </div>

            <div class="mt-8 text-center">
                <a href="{{ route('registration.flow') }}"
                    class="inline-flex items-center gap-2 text-sm font-semibold text-emerald-700 transition hover:text-emerald-500">
                    Pelajari alur selengkapnya
                    <x-icon name="arrow-right" class="h-4 w-4" />
                </a>
            </div>
        </div>
    </section>

    {{-- Jadwal pendaftaran & pengumuman --}}
    <section class="mx-auto grid max-w-7xl gap-6 px-4 py-16 sm:px-6 md:py-20 lg:grid-cols-2 lg:px-8">
        <div class="reveal">
            <div class="h-full rounded-2xl border border-slate-200 bg-white p-6 shadow-sm md:p-8">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">
                        <x-icon name="calendar-clock" />
                    </span>
                    <h3 class="text-lg font-semibold text-emerald-800">Informasi Jadwal Pendaftaran</h3>
                </div>

                <ul class="mt-5 space-y-3 text-sm text-slate-600">
                    <li class="flex items-start gap-3">
                        <x-icon name="info" class="mt-0.5 h-4 w-4 shrink-0 text-blue-500" />
                        Siswa hanya boleh memilih <span class="font-semibold text-emerald-700">satu organisasi</span>
                        dari lima pilihan.
                    </li>
                    <li class="flex items-start gap-3">
                        <x-icon name="info" class="mt-0.5 h-4 w-4 shrink-0 text-blue-500" />
                        Satu siswa hanya dapat memiliki <span class="font-semibold text-emerald-700">satu
                            pendaftaran</span>.
                    </li>
                    <li class="flex items-start gap-3">
                        <x-icon name="info" class="mt-0.5 h-4 w-4 shrink-0 text-blue-500" />
                        Seluruh seleksi dilakukan <span class="font-semibold text-emerald-700">secara offline</span> di
                        lingkungan sekolah.
                    </li>
                </ul>
            </div>
        </div>

        <div class="reveal">
            <x-announcement-indicator :state="$announcementState" :announcement="$resultAnnouncement" />

            <div class="mt-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-emerald-800">Pengumuman Terbaru</h3>
                    <a href="{{ route('announcements.index') }}"
                        class="text-sm font-semibold text-emerald-600 transition hover:text-emerald-400">Lihat semua</a>
                </div>

                @forelse ($announcements as $announcement)
                    <article
                        class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-emerald-300">
                        <div class="flex items-center gap-2 text-xs font-medium text-blue-500">
                            <x-icon name="calendar-days" class="h-3.5 w-3.5" />
                            {{ \Illuminate\Support\Carbon::parse($announcement->announcement_date)->isoFormat('D MMMM YYYY') }}
                        </div>
                        <h4 class="mt-2 font-semibold text-emerald-800">{{ $announcement->title }}</h4>
                        <p class="mt-1.5 text-sm leading-relaxed text-slate-600 line-clamp-2">
                            {{ $announcement->description }}
                        </p>
                    </article>
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-300 p-6 text-center text-sm text-slate-500">
                        Belum ada pengumuman yang diterbitkan.
                    </div>
                @endforelse
            </div>
        </div>
    </section>
@endsection
