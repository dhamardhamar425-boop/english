@extends('layouts.public')

@section('title', $organization->name)

@section('content')
    {{-- Header organisasi --}}
    <section class="border-b border-slate-200 bg-emerald-900 text-white">
        <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 md:py-16 lg:px-8">
            <div class="flex flex-col items-start gap-6 sm:flex-row sm:items-center">
                @if ($organization->logoUrl())
                    <img src="{{ $organization->logoUrl() }}" alt="Logo {{ $organization->name }}"
                        class="h-24 w-24 shrink-0 rounded-2xl bg-white/95 object-contain p-2 ring-1 ring-white/20" />
                @else
                    <span
                        class="flex h-24 w-24 shrink-0 items-center justify-center rounded-2xl bg-white/10 text-3xl font-bold ring-1 ring-white/20">
                        {{ $organization->initials() }}
                    </span>
                @endif
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-blue-300">Organisasi Siswa</p>
                    <h1 class="mt-1 text-3xl font-semibold tracking-tight md:text-4xl">{{ $organization->name }}</h1>
                    <p class="mt-3 max-w-2xl text-sm leading-relaxed text-emerald-200">
                        {{ $organization->description }}
                    </p>
                    <div class="mt-5 flex flex-wrap gap-3">
                        @if ($registrationOpen)
                            <a href="{{ route('registrations.create', ['organization' => $organization->slug]) }}"
                                class="inline-flex items-center gap-2 rounded-xl bg-blue-500 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-600 ring-1 ring-blue-400/40">
                                Daftar Sekarang
                                <x-icon name="arrow-right" class="h-4 w-4" />
                            </a>
                        @else
                            <span
                                class="inline-flex cursor-not-allowed items-center gap-2 rounded-xl bg-white/10 px-5 py-2.5 text-sm font-semibold text-white/60 ring-1 ring-white/15">
                                <x-icon name="lock" class="h-4 w-4" />
                                Pendaftaran Ditutup
                            </span>
                        @endif
                        @if ($organization->contact_link)
                            <a href="{{ $organization->contact_link }}" target="_blank" rel="noopener noreferrer"
                                class="inline-flex items-center gap-2 rounded-xl bg-white/10 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-white/20 ring-1 ring-white/15">
                                Informasi Kontak
                                <x-icon name="external-link" class="h-4 w-4" />
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 md:py-16 lg:px-8">
        <div class="grid gap-8 lg:grid-cols-3">
            <div class="space-y-8 lg:col-span-2">
                {{-- Visi & Misi --}}
                <div class="reveal rounded-2xl border border-slate-200 bg-white p-6 shadow-sm md:p-8">
                    <h2 class="flex items-center gap-2 text-lg font-semibold text-emerald-800">
                        <x-icon name="star" class="h-5 w-5 text-blue-500" />
                        Visi
                    </h2>
                    <p class="mt-3 leading-relaxed text-slate-600">{{ $organization->vision ?? '—' }}</p>

                    <h2 class="mt-8 flex items-center gap-2 text-lg font-semibold text-emerald-800">
                        <x-icon name="list-check" class="h-5 w-5 text-blue-500" />
                        Misi
                    </h2>
                    <p class="mt-3 whitespace-pre-line leading-relaxed text-slate-600">{{ $organization->mission ?? '—' }}</p>
                </div>

                {{-- Program Kerja & Kegiatan --}}
                <div class="grid gap-6 md:grid-cols-2">
                    <div class="reveal rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h2 class="flex items-center gap-2 text-lg font-semibold text-emerald-800">
                            <x-icon name="clipboard-list" class="h-5 w-5 text-blue-500" />
                            Program Kerja
                        </h2>
                        <p class="mt-3 whitespace-pre-line leading-relaxed text-slate-600">
                            {{ $organization->program_work ?? '—' }}</p>
                    </div>

                    <div class="reveal rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h2 class="flex items-center gap-2 text-lg font-semibold text-emerald-800">
                            <x-icon name="flag" class="h-5 w-5 text-blue-500" />
                            Kegiatan
                        </h2>
                        <p class="mt-3 leading-relaxed text-slate-600">{{ $organization->activities ?? '—' }}</p>

                        @if ($organization->organizationActivities->isNotEmpty())
                            <ul class="mt-4 space-y-3 border-t border-slate-100 pt-4">
                                @foreach ($organization->organizationActivities as $activity)
                                    <li class="flex items-start gap-3 text-sm">
                                        <span
                                            class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                                            <x-icon name="calendar-days" class="h-4 w-4" />
                                        </span>
                                        <span>
                                            <span class="block font-medium text-emerald-800">{{ $activity->title }}</span>
                                            <span class="block text-xs text-slate-500">
                                                @if ($activity->occurred_at)
                                                    {{ $activity->occurred_at->isoFormat('D MMMM YYYY') }} ·
                                                @endif
                                                {{ \Illuminate\Support\Str::limit($activity->description ?? '', 100) }}
                                            </span>
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>

                {{-- Galeri dokumentasi --}}
                <div class="reveal rounded-2xl border border-slate-200 bg-white p-6 shadow-sm md:p-8">
                    <h2 class="flex items-center gap-2 text-lg font-semibold text-emerald-800">
                        <x-icon name="image" class="h-5 w-5 text-blue-500" />
                        Galeri Dokumentasi
                    </h2>

                    @if ($organization->galleries->isNotEmpty())
                        <div class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-3">
                            @foreach ($organization->galleries as $gallery)
                                <figure class="group overflow-hidden rounded-xl border border-slate-200 bg-slate-50">
                                    <img src="{{ $gallery->imageUrl() }}" alt="{{ $gallery->title }}" loading="lazy"
                                        class="h-40 w-full object-cover transition duration-300 group-hover:scale-105" />
                                    <figcaption class="px-3 py-2 text-xs text-slate-600">{{ $gallery->title }}</figcaption>
                                </figure>
                            @endforeach
                        </div>
                    @else
                        <p class="mt-3 text-sm text-slate-500">Belum ada dokumentasi yang diunggah.</p>
                    @endif
                </div>
            </div>

            {{-- Sidebar informasi --}}
            <aside class="space-y-6">
                <div class="reveal rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-base font-semibold text-emerald-800">Persyaratan Pendaftaran</h2>
                    <p class="mt-3 whitespace-pre-line text-sm leading-relaxed text-slate-600">
                        {{ $organization->requirements ?? '—' }}</p>
                </div>

                <div class="reveal rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-base font-semibold text-emerald-800">Gabung Grup WhatsApp</h2>
                    <p class="mt-2 text-sm text-slate-600">
                        Link grup tersedia setelah pendaftaran berhasil, sesuai organisasi pilihan Anda.
                    </p>
                </div>

                @if ($organization->contact_link)
                    <div class="reveal rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h2 class="text-base font-semibold text-emerald-800">Informasi Kontak</h2>
                        <a href="{{ $organization->contact_link }}" target="_blank" rel="noopener noreferrer"
                            class="mt-3 inline-flex items-center gap-2 text-sm font-semibold text-emerald-700 transition hover:text-emerald-500">
                            Hubungi pengurus {{ $organization->name }}
                            <x-icon name="external-link" class="h-4 w-4" />
                        </a>
                    </div>
                @endif

                @if ($registrationOpen)
                    <a href="{{ route('registrations.create', ['organization' => $organization->slug]) }}"
                        class="flex items-center justify-center gap-2 rounded-xl bg-emerald-800 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700">
                        Daftar Sekarang
                        <x-icon name="arrow-right" class="h-4 w-4" />
                    </a>
                @else
                    <span
                        class="flex cursor-not-allowed items-center justify-center gap-2 rounded-xl bg-slate-200 px-5 py-3 text-sm font-semibold text-slate-500">
                        <x-icon name="lock" class="h-4 w-4" />
                        Pendaftaran Ditutup
                    </span>
                @endif
            </aside>
        </div>
    </div>
@endsection
