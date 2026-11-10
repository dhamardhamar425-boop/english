@extends('layouts.public')

@section('title', 'Pendaftaran Berhasil')

@section('content')
    <section class="bg-emerald-50">
        <div class="mx-auto max-w-4xl px-4 py-14 sm:px-6 md:py-16 lg:px-8">
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                {{-- Kepala --}}
                <div class="bg-emerald-800 px-6 py-10 text-center text-white md:px-10">
                    <span
                        class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-emerald-500/15 ring-1 ring-emerald-400/40">
                        <x-icon name="check" class="h-8 w-8 text-emerald-300" />
                    </span>
                    <h1 class="mt-5 text-2xl font-semibold md:text-3xl">Pendaftaran Berhasil!</h1>
                    <p class="mx-auto mt-2 max-w-xl text-sm text-emerald-200">
                        Data pendaftaran Anda sudah masuk ke sistem. Simpan nomor pendaftaran di bawah ini.
                    </p>

                    <div class="mt-6 inline-block rounded-xl bg-white/10 px-6 py-4 ring-1 ring-white/15">
                        <p class="text-xs uppercase tracking-wider text-blue-300">Nomor Pendaftaran</p>
                        <p class="mt-1 font-mono text-2xl font-bold tracking-wider text-white">
                            {{ $registration->registration_number }}
                        </p>
                    </div>
                </div>

                {{-- Ringkasan data --}}
                <div class="px-6 py-8 md:px-10">
                    <h2 class="text-base font-semibold text-emerald-800">Ringkasan Data Pendaftaran</h2>
                    <dl class="mt-4 grid gap-x-8 gap-y-4 text-sm sm:grid-cols-2">
                        <div>
                            <dt class="text-slate-500">Nama Lengkap</dt>
                            <dd class="mt-0.5 font-medium text-emerald-800">{{ $registration->full_name }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">NIS</dt>
                            <dd class="mt-0.5 font-medium text-emerald-800">{{ $registration->nis }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">Kelas</dt>
                            <dd class="mt-0.5 font-medium text-emerald-800">{{ $registration->class }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">Organisasi Pilihan</dt>
                            <dd class="mt-0.5 font-medium text-emerald-800">{{ $registration->organization->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">Tanggal Pendaftaran</dt>
                            <dd class="mt-0.5 font-medium text-emerald-800">
                                {{ $registration->registered_at->isoFormat('D MMMM YYYY, H:i') }} WIB
                            </dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-slate-500">Status</dt>
                            <dd class="mt-1.5">
                                <x-status-badge status="{{ $registration->status->value }}" />
                            </dd>
                        </div>
                    </dl>

                    {{-- Link grup WhatsApp --}}
                    @if ($registration->organization->whatsapp_group_link)
                        <div class="mt-8 rounded-2xl border border-emerald-200 bg-emerald-50 p-5">
                            <div class="flex items-start gap-3">
                                <x-icon name="send" class="mt-0.5 h-5 w-5 shrink-0 text-emerald-700" />
                                <div>
                                    <p class="text-sm font-semibold text-emerald-900">Gabung Grup WhatsApp
                                        {{ $registration->organization->name }}</p>
                                    <p class="mt-1 text-sm leading-relaxed text-emerald-800">
                                        Link grup sesuai organisasi pilihan Anda. Gunakan grup ini untuk informasi
                                        jadwal seleksi offline dari pengurus.
                                    </p>
                                    <a href="{{ $registration->organization->whatsapp_group_link }}" target="_blank"
                                        rel="noopener noreferrer"
                                        class="mt-3 inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-700">
                                        Buka Link Grup WhatsApp
                                        <x-icon name="external-link" class="h-4 w-4" />
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Petunjuk --}}
                    <div class="mt-6 grid gap-4 md:grid-cols-2">
                        <div class="rounded-2xl border border-slate-200 p-5">
                            <p class="flex items-center gap-2 text-sm font-semibold text-emerald-800">
                                <x-icon name="clipboard-check" class="h-4 w-4 text-blue-500" />
                                Petunjuk Seleksi Offline
                            </p>
                            <p class="mt-2 text-sm leading-relaxed text-slate-600">
                                Hadir sesuai jadwal yang diinformasikan pengurus melalui grup WhatsApp. Seluruh
                                tahapan seleksi dilaksanakan di lingkungan sekolah.
                            </p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 p-5">
                            <p class="flex items-center gap-2 text-sm font-semibold text-emerald-800">
                                <x-icon name="search" class="h-4 w-4 text-blue-500" />
                                Cara Cek Status Pendaftaran
                            </p>
                            <p class="mt-2 text-sm leading-relaxed text-slate-600">
                                Buka halaman <a href="{{ route('status.check') }}"
                                    class="font-semibold text-emerald-700 hover:underline">Cek Status Pendaftaran</a>,
                                lalu masukkan nomor pendaftaran atau NIS Anda untuk melihat perkembangan terbaru.
                            </p>
                        </div>
                    </div>

                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                        <a href="{{ route('status.check') }}"
                            class="inline-flex flex-1 items-center justify-center gap-2 rounded-xl bg-emerald-800 px-5 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700">
                            Cek Status Pendaftaran
                            <x-icon name="arrow-right" class="h-4 w-4" />
                        </a>
                        <a href="{{ route('home') }}"
                            class="inline-flex flex-1 items-center justify-center gap-2 rounded-xl border border-slate-300 px-5 py-3 text-sm font-semibold text-emerald-700 transition hover:border-emerald-500 hover:bg-emerald-50">
                            Kembali ke Beranda
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
