@extends('layouts.public')

@section('title', 'Alur Pendaftaran')

@section('content')
    <section class="border-b border-slate-200 bg-emerald-50">
        <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 md:py-16 lg:px-8">
            <x-section-heading title="Alur Pendaftaran"
                subtitle="Ikuti enam tahapan berikut secara berurutan. Seleksi dilakukan offline, website hanya mengelola administrasi dan hasil seleksi." />
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-14 sm:px-6 md:py-16 lg:px-8">
        <x-flow-steps detailed />

        <div class="mt-10 grid gap-6 lg:grid-cols-2">
            <div class="reveal rounded-2xl border border-slate-200 bg-white p-6 shadow-sm md:p-8">
                <h3 class="flex items-center gap-2 text-lg font-semibold text-emerald-800">
                    <x-icon name="calendar-clock" class="h-5 w-5 text-blue-500" />
                    Jadwal Pendaftaran
                </h3>
                </div>
            </div>

            <div class="reveal">
                <x-announcement-indicator :state="$announcementState" :announcement="$resultAnnouncement" />
            </div>
        </div>

        <div class="mt-10 flex flex-col items-center justify-center gap-4 rounded-2xl bg-emerald-800 p-8 text-center">
            <h3 class="text-xl font-semibold text-white">Langkah pertama: pilih organisasimu</h3>
            <p class="max-w-xl text-sm text-emerald-200">
                Pelajari kelima organisasi, lalu isi formulir pendaftaran sebelum periode ditutup.
            </p>
            <div class="flex flex-col gap-3 sm:flex-row">
                <a href="{{ route('organizations.index') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-white px-5 py-2.5 text-sm font-semibold text-emerald-800 transition hover:bg-blue-50">
                    Lihat Organisasi
                </a>
                @if ($registrationState === 'buka')
                    <a href="{{ route('registrations.create') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-500 px-5 py-2.5 text-sm font-semibold text-white ring-1 ring-blue-400/40 transition hover:bg-blue-600">
                        Daftar Sekarang
                        <x-icon name="arrow-right" class="h-4 w-4" />
                    </a>
                @endif
            </div>
        </div>
    </section>
@endsection
