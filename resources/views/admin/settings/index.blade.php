@extends('layouts.admin')

@section('title', 'Pengaturan Umum')
@section('subtitle', 'Identitas sekolah, periode pendaftaran & pengumuman hasil seleksi')

@section('content')
    <div class="max-w-2xl space-y-6">
        <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
            @csrf
            @method('POST')

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="font-display text-lg font-extrabold text-emerald-900">Identitas Sekolah</h3>

                <div class="mt-4">
                    <label for="school_name" class="mb-1 block text-xs font-semibold text-slate-600">Nama Sekolah</label>
                    <input type="text" name="school_name" id="school_name" value="{{ old('school_name', $settings['school_name']) }}" required
                           class="w-full rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500">
                    @error('school_name') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                    <p class="mt-1 text-xs text-slate-500">Nama ini tampil di situs publik & dashboard admin.</p>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="font-display text-lg font-extrabold text-emerald-900">Periode Pendaftaran</h3>

                <label class="mt-4 flex items-center gap-2 text-sm font-semibold text-slate-700">
                    <input type="hidden" name="registration_is_open" value="0">
                    <input type="checkbox" name="registration_is_open" value="1" @checked(old('registration_is_open', $settings['registration_is_open']) === '1')
                           class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                    Buka pendaftaran siswa
                </label>

                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="registration_start" class="mb-1 block text-xs font-semibold text-slate-600">Tanggal Mulai</label>
                        <input type="date" name="registration_start" id="registration_start"
                               value="{{ old('registration_start', $settings['registration_start']) }}" required
                               class="w-full rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500">
                        @error('registration_start') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="registration_end" class="mb-1 block text-xs font-semibold text-slate-600">Tanggal Berakhir</label>
                        <input type="date" name="registration_end" id="registration_end"
                               value="{{ old('registration_end', $settings['registration_end']) }}" required
                               class="w-full rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500">
                        @error('registration_end') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <p class="mt-3 text-xs text-slate-500">
                    Status saat ini:
                    @php $state = \App\Models\Setting::registrationState(); @endphp
                    <span @class([
                        'rounded-full px-2 py-0.5 text-[11px] font-bold',
                        'bg-success-100 text-success-700' => $state === 'buka',
                        'bg-amber-100 text-amber-700' => $state === 'belum_dimulai',
                        'bg-danger-100 text-danger-700' => $state === 'ditutup',
                    ])>
                        {{ ['buka' => 'Terbuka', 'belum_dimulai' => 'Belum Dimulai', 'ditutup' => 'Ditutup'][$state] ?? $state }}
                    </span>
                </p>

                <p class="mt-2 text-xs text-slate-500">
                    Pendaftaran otomatis ditutup setelah jadwal pengumuman hasil seleksi tiba. Untuk membuka kembali,
                    undur jadwal pada panel
                    <a href="#pengumuman-hasil-seleksi"
                        class="font-semibold text-emerald-700 hover:text-emerald-500">Pengumuman Hasil Seleksi</a>
                    di halaman ini.
                </p>
            </div>

            <button type="submit"
                    class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-900 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-800">
                <x-icon name="check" class="h-4 w-4" />
                Simpan Pengaturan
            </button>
        </form>

        {{-- Pengumuman hasil seleksi serentak --}}
        <div id="pengumuman-hasil-seleksi" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div class="flex items-start gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">
                        <x-icon name="megaphone" class="h-5 w-5" />
                    </span>
                    <div>
                        <h3 class="font-display text-lg font-extrabold text-emerald-900">Pengumuman Hasil Seleksi</h3>
                        <p class="mt-0.5 text-xs text-slate-500">
                            Satu jadwal untuk <span class="font-semibold text-emerald-700">semua organisasi sekaligus</span>.
                        </p>
                    </div>
                </div>

                @php
                    $stateKey = $announcementState?->value ?? 'not_scheduled';
                    $stateStyle = [
                        'not_scheduled' => 'bg-slate-100 text-slate-600',
                        'not_open' => 'bg-amber-100 text-amber-700',
                        'ongoing' => 'bg-blue-100 text-blue-700',
                        'opened' => 'bg-success-100 text-success-700',
                    ][$stateKey] ?? 'bg-slate-100 text-slate-600';
                @endphp
                <span class="rounded-full px-2.5 py-1 text-[11px] font-bold {{ $stateStyle }}">
                    {{ $announcementState?->label() ?? 'Belum dijadwalkan' }}
                </span>
            </div>

            @if ($resultAnnouncement?->scheduledAt())
                <p class="mt-4 text-sm text-slate-600">
                    Jadwal saat ini:
                    <span class="font-semibold text-emerald-800">
                        {{ $resultAnnouncement->scheduledAt()->isoFormat('dddd, D MMMM YYYY') }},
                        pukul {{ $resultAnnouncement->scheduledAt()->format('H:i') }} WIB
                    </span>
                </p>
            @else
                <p class="mt-4 text-sm text-slate-500">Belum ada jadwal pengumuman hasil seleksi.</p>
            @endif

            <form method="POST" action="{{ route('admin.settings.result-announcement.update') }}" class="mt-5 space-y-4">
                @csrf

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="announcement_date" class="mb-1 block text-xs font-semibold text-slate-600">Tanggal Pengumuman</label>
                        <input type="date" name="announcement_date" id="announcement_date"
                               value="{{ old('announcement_date', $resultAnnouncement?->announcement_date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required
                               class="w-full rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500">
                        @error('announcement_date', 'resultAnnouncement') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="announcement_time" class="mb-1 block text-xs font-semibold text-slate-600">Waktu (WIB)</label>
                        <input type="time" name="announcement_time" id="announcement_time"
                               value="{{ old('announcement_time', substr((string) $resultAnnouncement?->announcement_time, 0, 5) ?: '13:00') }}" required
                               class="w-full rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500">
                        @error('announcement_time', 'resultAnnouncement') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label for="announcement_description" class="mb-1 block text-xs font-semibold text-slate-600">Keterangan (opsional)</label>
                    <textarea name="description" id="announcement_description" rows="2"
                              class="w-full rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500">{{ old('description', $resultAnnouncement?->description) }}</textarea>
                    @error('description', 'resultAnnouncement') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                </div>

                <p class="text-xs text-slate-500">
                    Hasil seleksi otomatis tampil ke siswa saat jadwal tiba, dan pendaftaran otomatis ditutup.
                    Mengundur jadwal akan membuka kembali pendaftaran selama periode masih berjalan.
                </p>

                <div class="flex flex-wrap gap-2">
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-800">
                        <x-icon name="calendar-days" class="h-4 w-4" />
                        Simpan Jadwal
                    </button>
                </div>
            </form>

            <form method="POST" action="{{ route('admin.settings.result-announcement.open') }}" class="mt-3"
                  onsubmit="return confirm('Buka pengumuman hasil seleksi sekarang? Hasil akan langsung terlihat oleh semua peserta dan pendaftaran ditutup.')">
                @csrf
                <button type="submit"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">
                    <x-icon name="megaphone" class="h-4 w-4" />
                    Buka Pengumuman Sekarang
                </button>
            </form>
        </div>
    </div>
@endsection
