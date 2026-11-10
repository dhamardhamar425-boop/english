@extends('layouts.public')

@section('title', 'Pendaftaran')

@section('content')
    @php
        $orgColor = $organization?->color ?? '#1e3a8a';
        $orgLabel = $organization?->name ?? 'Pendaftaran Anggota Baru';
    @endphp
    <section class="border-b border-slate-200">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <x-section-heading title="{{ $orgLabel }}"
                subtitle="Isi seluruh data dengan benar. Setiap siswa hanya boleh memiliki satu pendaftaran."
                color="{{ $orgColor }}" />
        </div>
    </section>

    <style>
        #form-pendaftaran-{{ $organization?->slug ?? 'umum' }} input:focus,
        #form-pendaftaran-{{ $organization?->slug ?? 'umum' }} textarea:focus,
        #form-pendaftaran-{{ $organization?->slug ?? 'umum' }} select:focus {
            border-color: {{ $orgColor }};
            box-shadow: 0 0 0 2px {{ $orgColor }}40;
        }
        #form-pendaftaran-{{ $organization?->slug ?? 'umum' }} button[type="submit"] {
            background-color: {{ $orgColor }};
        }
        #form-pendaftaran-{{ $organization?->slug ?? 'umum' }} button[type="submit"]:hover {
            filter: brightness(0.88);
        }
        #form-pendaftaran-{{ $organization?->slug ?? 'umum' }} input[type="radio"]:checked {
            border-color: {{ $orgColor }};
            background-color: {{ $orgColor }};
        }
    </style>

    <section class="mx-auto max-w-4xl px-4 py-12 sm:px-6 md:py-14 lg:px-8">
        @if (! $registrationOpen)
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-8 md:p-12 text-center shadow-sm">
                <div class="inline-flex items-center justify-center gap-2 rounded-full bg-emerald-100 px-4 py-1.5 text-xs font-semibold text-emerald-800 mb-4">
                    <x-icon name="lock" class="h-3.5 w-3.5" />
                    Periode Pendaftaran Ditutup
                </div>
                <h2 class="text-2xl font-extrabold text-emerald-900 md:text-3xl">
                    PENDAFTARAN PERIODE (PERIODE TAHUN) TELAH DITUTUP, SAMPAI JUMPA DI PERIODE TAHUN DEPAN
                </h2>
                @php
                    $start = \App\Models\Setting::registrationStart();
                    $end = \App\Models\Setting::registrationEnd();
                @endphp
                @if ($start && $end)
                    <p class="mt-4 text-sm text-emerald-700/80">
                        Periode pendaftaran {{ $start->isoFormat('D MMMM YYYY') }} – {{ $end->isoFormat('D MMMM YYYY') }}
                        telah berakhir.
                    </p>
                @endif
                <p class="mt-3 text-sm text-emerald-600">
                    Pantau pengumuman terbaru untuk informasi pendaftaran periode tahun depan.
                </p>
            </div>
        @else
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm md:p-8">
                <form id="form-pendaftaran-{{ $organization?->slug ?? 'umum' }}"
                    method="POST"
                    action="{{ route('registrations.store', ['organization' => $organization?->slug]) }}"
                    class="space-y-6">
                    @csrf

                {{-- Identitas --}}
                <div>
                    <p class="mb-4 text-xs font-semibold uppercase tracking-wider text-blue-500">Data Diri</p>
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label for="full_name" class="mb-1.5 block text-sm font-medium text-slate-700">Nama
                                Lengkap <span class="text-red-500">*</span></label>
                            <input id="full_name" type="text" name="full_name"
                                value="{{ old('full_name') }}" required maxlength="255" placeholder="Nama lengkap sesuai kartu pelajar"
                                class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20" />
                            @error('full_name')
                                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="nis" class="mb-1.5 block text-sm font-medium text-slate-700">NIS <span
                                    class="text-red-500">*</span></label>
                            <input id="nis" type="text" name="nis" value="{{ old('nis') }}" required
                                maxlength="20" inputmode="numeric" placeholder="Contoh: 102435"
                                class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20" />
                            @error('nis')
                                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="nik" class="mb-1.5 block text-sm font-medium text-slate-700">NIK <span
                                    class="text-red-500">*</span></label>
                            <input id="nik" type="text" name="nik" value="{{ old('nik') }}" required
                                maxlength="16" inputmode="numeric" placeholder="16 digit NIK"
                                class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20" />
                            <p class="mt-1 text-xs text-slate-400">NIK bersifat rahasia dan tidak pernah ditampilkan
                                kepada publik.</p>
                            @error('nik')
                                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <span class="mb-1.5 block text-sm font-medium text-slate-700">Jenis Kelamin <span
                                    class="text-red-500">*</span></span>
                            <div class="flex gap-4 rounded-xl border border-slate-300 px-4 py-2.5">
                                <label class="flex items-center gap-2 text-sm text-slate-700">
                                    <input type="radio" name="gender" value="Laki-laki"
                                        @checked(old('gender') === 'Laki-laki') required
                                        class="text-emerald-700 focus:ring-emerald-500/20" />
                                    Laki-laki
                                </label>
                                <label class="flex items-center gap-2 text-sm text-slate-700">
                                    <input type="radio" name="gender" value="Perempuan"
                                        @checked(old('gender') === 'Perempuan') required
                                        class="text-emerald-700 focus:ring-emerald-500/20" />
                                    Perempuan
                                </label>
                            </div>
                            @error('gender')
                                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="class" class="mb-1.5 block text-sm font-medium text-slate-700">Kelas <span
                                    class="text-red-500">*</span></label>
                            <input id="class" type="text" name="class" value="{{ old('class') }}" required
                                maxlength="30" placeholder="Contoh: XI-TKJ-1"
                                class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20" />
                            @error('class')
                                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="phone" class="mb-1.5 block text-sm font-medium text-slate-700">Nomor
                                WhatsApp <span class="text-red-500">*</span></label>
                            <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" required
                                maxlength="20" inputmode="tel" placeholder="Contoh: 081234567890"
                                class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20" />
                            @error('phone')
                                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label for="address" class="mb-1.5 block text-sm font-medium text-slate-700">Alamat <span
                                    class="text-red-500">*</span></label>
                            <textarea id="address" name="address" rows="3" required maxlength="500" placeholder="Alamat rumah lengkap"
                                class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">{{ old('address') }}</textarea>
                            @error('address')
                                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Alasan memilih organisasi --}}
                <div class="border-t border-slate-100 pt-6">
                    <div class="mt-5">
                        <label for="reason" class="mb-1.5 block text-sm font-medium text-slate-700">Alasan memilih
                            organisasi <span class="text-red-500">*</span></label>
                        <textarea id="reason" name="reason" rows="4" required maxlength="1000" placeholder="Ceritakan alasan dan motivasi Anda memilih organisasi ini"
                            class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">{{ old('reason') }}</textarea>
                        @error('reason')
                            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="border-t border-slate-100 pt-6">
                    <label class="flex items-start gap-3 text-sm text-slate-600">
                        <input type="checkbox" required
                            class="mt-0.5 h-4 w-4 rounded border-slate-300 text-emerald-700 focus:ring-emerald-500/20" />
                        <span>Saya menyatakan data yang saya isi benar dan bersedia mengikuti seluruh tahapan seleksi
                            offline sesuai jadwal organisasi yang saya pilih.</span>
                    </label>

                    <button type="submit" @disabled(! $registrationOpen)
                        class="mt-6 inline-flex w-full items-center justify-center gap-2 rounded-xl px-6 py-3 text-sm font-semibold text-white transition focus:outline-none disabled:cursor-not-allowed disabled:bg-slate-300 disabled:text-slate-500"
                        style="background-color: {{ $orgColor }};">
                        Kirim Pendaftaran
                        <x-icon name="send" class="h-4 w-4" />
                    </button>
                </div>
            </form>
        </div>
    </section>
        @endif
@endsection
