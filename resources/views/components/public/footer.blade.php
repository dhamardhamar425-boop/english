@php
    $schoolName = \App\Models\Setting::cached('school_name', config('app.name'));
    $schoolAddress = \App\Models\Setting::cached('school_address', '');
    $schoolPhone = \App\Models\Setting::cached('school_phone', '');
    $schoolEmail = \App\Models\Setting::cached('school_email', '');
    $schoolTagline = \App\Models\Setting::cached('school_tagline', '');
@endphp

<footer class="border-t border-emerald-700/60 bg-emerald-900 text-emerald-200">
    <div class="mx-auto grid max-w-7xl gap-10 px-4 py-12 sm:px-6 md:grid-cols-3 lg:px-8">
        <div>
            <div class="flex items-center gap-3">
                <span
                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/10 text-[11px] font-bold text-white ring-1 ring-white/20">SMK</span>
                <div>
                    <p class="text-sm font-semibold text-white">{{ $schoolName }}</p>
                    <p class="text-xs text-emerald-300">{{ $schoolTagline !== '' ? $schoolTagline : 'Portal Organisasi Siswa' }}</p>
                </div>
            </div>
            <p class="mt-4 text-sm leading-relaxed text-emerald-300">
                Portal resmi pendaftaran dan seleksi anggota baru organisasi siswa. Informasi organisasi,
                pendaftaran, dan pengumuman hasil seleksi tersedia di website ini.
            </p>
        </div>

        <div>
            <p class="text-sm font-semibold uppercase tracking-wider text-white">Tautan Cepat</p>
            <ul class="mt-4 space-y-2 text-sm">
                <li><a href="{{ route('home') }}" class="transition hover:text-white">Beranda</a></li>
                <li><a href="{{ route('organizations.index') }}" class="transition hover:text-white">Organisasi</a></li>
                <li><a href="{{ route('registration.flow') }}" class="transition hover:text-white">Alur Pendaftaran</a></li>
                <li><a href="{{ route('announcements.index') }}" class="transition hover:text-white">Pengumuman</a></li>
                <li><a href="{{ route('status.check') }}" class="transition hover:text-white">Cek Status Pendaftaran</a></li>
                <li><a href="{{ route('pages.about') }}" class="transition hover:text-white">Tentang</a></li>
            </ul>
        </div>

        <div>
            <p class="text-sm font-semibold uppercase tracking-wider text-white">Kontak Sekolah</p>
            <ul class="mt-4 space-y-3 text-sm">
                @if ($schoolAddress !== '')
                    <li class="flex items-start gap-3">
                        <x-icon name="map-pin" class="mt-0.5 h-4 w-4 shrink-0 text-blue-300" />
                        <span>{{ $schoolAddress }}</span>
                    </li>
                @endif
                @if ($schoolPhone !== '')
                    <li class="flex items-center gap-3">
                        <x-icon name="phone" class="h-4 w-4 shrink-0 text-blue-300" />
                        <span>{{ $schoolPhone }}</span>
                    </li>
                @endif
                @if ($schoolEmail !== '')
                    <li class="flex items-center gap-3">
                        <x-icon name="mail" class="h-4 w-4 shrink-0 text-blue-300" />
                        <a href="mailto:{{ $schoolEmail }}" class="transition hover:text-white">{{ $schoolEmail }}</a>
                    </li>
                @endif
            </ul>
        </div>
    </div>

    <div class="border-t border-white/10">
        <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-2 px-4 py-5 text-xs text-emerald-400 sm:flex-row sm:px-6 lg:px-8">
            <p>© {{ now()->year }} {{ $schoolName }}. Seluruh hak cipta dilindungi.</p>
            <a href="{{ route('login') }}" class="transition hover:text-white">Login Admin</a>
        </div>
    </div>
</footer>
