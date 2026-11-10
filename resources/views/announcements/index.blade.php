
@extends('layouts.public')

@section('title', 'Pengumuman')

@section('content')
    {{-- HERO --}}
    <section class="relative overflow-hidden border-b border-emerald-100 bg-gradient-to-br from-emerald-50 via-white to-emerald-50">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 md:py-16 lg:px-8">
            <div class="max-w-3xl">
                <div class="mb-4 inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-white px-3 py-1.5 text-xs font-semibold text-emerald-700 shadow-sm">
                    <x-icon name="megaphone" class="h-4 w-4" />
                    Pusat Informasi Seleksi
                </div>

                <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">
                    Pengumuman
                    <span class="text-emerald-700">Seleksi Anggota</span>
                </h1>

                <p class="mt-4 max-w-2xl text-sm leading-7 text-slate-600 sm:text-base">
                    Temukan informasi terbaru seputar pendaftaran, jadwal seleksi,
                    dan hasil akhir calon anggota organisasi siswa.
                </p>
            </div>
        </div>
    </section>

    {{-- MAIN CONTENT --}}
    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 md:py-14 lg:px-8">
        <div class="grid gap-8 lg:grid-cols-12">

            {{-- SIDEBAR --}}
            <aside class="lg:col-span-4">
                <div class="space-y-5 lg:sticky lg:top-24">

                    {{-- STATUS --}}
                    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <div class="border-b border-slate-100 px-5 py-4">
                            <h2 class="flex items-center gap-2 font-bold text-slate-800">
                                <x-icon name="activity" class="h-5 w-5 text-emerald-700" />
                                Status Pengumuman
                            </h2>
                            <p class="mt-1 text-xs text-slate-500">
                                Informasi terkini hasil seleksi.
                            </p>
                        </div>

                        <div class="p-5">
                            <x-announcement-indicator
                                :state="$announcementState"
                                :announcement="$resultAnnouncement"
                            />
                        </div>
                    </div>

                    {{-- PANDUAN --}}
                    <div class="rounded-2xl border border-blue-100 bg-blue-50 p-5">
                        <div class="flex items-start gap-3">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-blue-700">
                                <x-icon name="info" class="h-5 w-5" />
                            </div>

                            <div>
                                <h3 class="font-bold text-blue-900">
                                    Perlu diketahui
                                </h3>
                                <p class="mt-2 text-sm leading-6 text-blue-800">
                                    Hasil seleksi hanya bisa dilihat setelah pengumuman resmi dibuka.
                                    Jika belum waktunya, silakan periksa kembali sesuai jadwal.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- CTA --}}
                    <div class="rounded-2xl bg-emerald-900 p-5 text-white shadow-sm">
                        <div class="flex items-center gap-2">
                            <x-icon name="clipboard-check" class="h-5 w-5" />
                            <h3 class="font-bold">Ingin melihat statusmu?</h3>
                        </div>

                        <p class="mt-2 text-sm leading-6 text-emerald-100">
                            Periksa perkembangan pendaftaranmu melalui halaman status.
                        </p>

                        <a href="{{ route('status.check') }}"
                            class="mt-5 flex w-full items-center justify-center gap-2 rounded-xl bg-white px-4 py-3 text-sm font-bold text-emerald-900 transition hover:bg-emerald-50 focus:outline-none focus:ring-4 focus:ring-emerald-400/40">
                            Cek Status Pendaftaran
                            <x-icon name="arrow-right" class="h-4 w-4" />
                        </a>
                    </div>
                </div>
            </aside>

            {{-- ANNOUNCEMENTS --}}
            <main class="lg:col-span-8">
                <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
                    <div>
                        <h2 class="text-xl font-bold text-slate-900">
                            Informasi Terbaru
                        </h2>
                        <p class="mt-1 text-sm text-slate-500">
                            Baca informasi resmi yang telah diterbitkan panitia.
                        </p>
                    </div>

                    <span class="rounded-full bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-600">
                        {{ $announcements->total() }} Pengumuman
                    </span>
                </div>

                <div class="space-y-4">
                    @forelse ($announcements as $announcement)
                        <article class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-emerald-200 hover:shadow-md sm:p-6">

                            {{-- META --}}
                            <div class="flex flex-wrap items-center gap-3">
                                <span class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-700">
                                    <x-icon name="megaphone" class="h-3.5 w-3.5" />
                                    Informasi Resmi
                                </span>

                                <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-500">
                                    <x-icon name="calendar-days" class="h-4 w-4" />
                                    {{ \Illuminate\Support\Carbon::parse($announcement->announcement_date)->isoFormat('dddd, D MMMM YYYY') }}
                                </span>
                            </div>

                            {{-- CONTENT --}}
                            <h3 class="mt-4 text-lg font-bold leading-snug text-slate-900 transition group-hover:text-emerald-800 sm:text-xl">
                                {{ $announcement->title }}
                            </h3>

                            <p class="mt-3 whitespace-pre-line text-sm leading-7 text-slate-600">
                                {{ $announcement->description }}
                            </p>

                            <div class="mt-5 border-t border-slate-100 pt-4">
                                <span class="inline-flex items-center gap-2 text-xs font-medium text-slate-400">
                                    <x-icon name="shield-check" class="h-4 w-4 text-emerald-600" />
                                    Diterbitkan secara resmi oleh panitia
                                </span>
                            </div>
                        </article>
                    @empty
                        {{-- EMPTY STATE --}}
                        <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center">
                            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-emerald-50">
                                <x-icon name="bell" class="h-8 w-8 text-emerald-600" />
                            </div>

                            <h3 class="mt-5 text-lg font-bold text-slate-800">
                                Belum Ada Pengumuman
                            </h3>

                            <p class="mx-auto mt-2 max-w-sm text-sm leading-6 text-slate-500">
                                Saat ini belum ada informasi yang diterbitkan.
                                Silakan kunjungi halaman ini kembali untuk melihat kabar terbaru.
                            </p>

                            <a href="{{ route('status.check') }}"
                                class="mt-6 inline-flex items-center gap-2 rounded-xl bg-emerald-700 px-5 py-3 text-sm font-semibold text-white transition hover:bg-emerald-800">
                                Cek Status Pendaftaran
                                <x-icon name="arrow-right" class="h-4 w-4" />
                            </a>
                        </div>
                    @endforelse
                </div>

                {{-- PAGINATION --}}
                @if ($announcements->hasPages())
                    <div class="mt-8 rounded-2xl border border-slate-200 bg-white p-4">
                        {{ $announcements->withQueryString()->links() }}
                    </div>
                @endif
            </main>
        </div>
    </section>
@endsection
